<?php

declare(strict_types=1);

class EventController extends BaseController
{
    public function index(): void
    {
        $db = connect_db();
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : null;
        $events = Event::allPublic($db, $q);
        $this->view('event/index', ['events' => $events, 'q' => $q ?? '']);
    }

    public function detail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id < 1) {
            flash('error', 'Invalid event.');
            redirect(url('event', 'index'));
        }
        $db = connect_db();
        $event = Event::find($db, $id);
        if (!$event) {
            flash('error', 'Event not found.');
            redirect(url('event', 'index'));
        }
        $this->view('event/detail', ['event' => $event]);
    }

    public function mine(): void
    {
        require_role('organiser');
        $user = current_user();
        $db = connect_db();
        $events = Event::forOrganiser($db, $user['id']);
        $this->view('event/mine', ['events' => $events]);
    }

    public function create(): void
    {
        require_role('organiser');
        $db = connect_db();
        $categories = Category::all($db);
        $this->view('event/form', ['categories' => $categories, 'event' => null]);
    }

    public function store(): void
    {
        require_role('organiser');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('event', 'create'));
        }
        $user = current_user();
        $db = connect_db();

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $eventDate = trim((string) ($_POST['event_date'] ?? ''));
        $location = trim((string) ($_POST['location'] ?? ''));
        $ticketPrice = (float) ($_POST['ticket_price'] ?? 0);
        $totalSeats = (int) ($_POST['total_seats'] ?? 0);

        if ($title === '' || $location === '' || $eventDate === '' || $categoryId < 1) {
            flash('error', 'Please fill all required fields.');
            redirect(url('event', 'create'));
        }
        if ($totalSeats < 1) {
            flash('error', 'Total seats must be at least 1.');
            redirect(url('event', 'create'));
        }
        if (!Category::find($db, $categoryId)) {
            flash('error', 'Invalid category.');
            redirect(url('event', 'create'));
        }
        if ($ticketPrice < 0) {
            $ticketPrice = 0;
        }

        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $imageName = save_event_upload($_FILES['image']);
            if ($imageName === null) {
                flash('error', 'Image upload failed. Use JPG, PNG or WEBP under 2MB.');
                redirect(url('event', 'create'));
            }
        }

        if (!Event::create($db, $user['id'], $categoryId, $title, $description, $eventDate, $location, $ticketPrice, $totalSeats, $imageName)) {
            flash('error', 'Could not create event.');
            redirect(url('event', 'create'));
        }
        flash('success', 'Event created.');
        redirect(url('event', 'mine'));
    }

    public function edit(): void
    {
        require_role('organiser');
        $id = (int) ($_GET['id'] ?? 0);
        if ($id < 1) {
            redirect(url('event', 'mine'));
        }
        $user = current_user();
        $db = connect_db();
        $event = Event::find($db, $id);
        if (!$event || (int) $event['organiser_id'] !== $user['id']) {
            flash('error', 'Event not found.');
            redirect(url('event', 'mine'));
        }
        $categories = Category::all($db);
        $this->view('event/form', ['categories' => $categories, 'event' => $event]);
    }

    public function update(): void
    {
        require_role('organiser');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('event', 'mine'));
        }
        $user = current_user();
        $db = connect_db();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1) {
            redirect(url('event', 'mine'));
        }
        $existing = Event::find($db, $id);
        if (!$existing || (int) $existing['organiser_id'] !== $user['id']) {
            flash('error', 'Event not found.');
            redirect(url('event', 'mine'));
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $eventDate = trim((string) ($_POST['event_date'] ?? ''));
        $location = trim((string) ($_POST['location'] ?? ''));
        $ticketPrice = (float) ($_POST['ticket_price'] ?? 0);
        $totalSeats = (int) ($_POST['total_seats'] ?? 0);

        if ($title === '' || $location === '' || $eventDate === '' || $categoryId < 1) {
            flash('error', 'Please fill all required fields.');
            redirect(url('event', 'edit', ['id' => $id]));
        }
        if ($totalSeats < 1) {
            flash('error', 'Total seats must be at least 1.');
            redirect(url('event', 'edit', ['id' => $id]));
        }
        if (!Category::find($db, $categoryId)) {
            flash('error', 'Invalid category.');
            redirect(url('event', 'edit', ['id' => $id]));
        }
        if ($ticketPrice < 0) {
            $ticketPrice = 0;
        }

        $replaceImage = false;
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $imagePath = save_event_upload($_FILES['image']);
            if ($imagePath === null) {
                flash('error', 'Image upload failed. Use JPG, PNG or WEBP under 2MB.');
                redirect(url('event', 'edit', ['id' => $id]));
            }
            $replaceImage = true;
            if (!empty($existing['image'])) {
                $old = UPLOAD_PATH . '/' . $existing['image'];
                if (is_file($old)) {
                    @unlink($old);
                }
            }
        }

        if (!Event::update($db, $id, $user['id'], $categoryId, $title, $description, $eventDate, $location, $ticketPrice, $totalSeats, $imagePath, $replaceImage)) {
            flash('error', 'Could not update event.');
            redirect(url('event', 'edit', ['id' => $id]));
        }
        flash('success', 'Event updated.');
        redirect(url('event', 'mine'));
    }

    public function delete(): void
    {
        require_role('organiser');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('event', 'mine'));
        }
        $user = current_user();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1) {
            redirect(url('event', 'mine'));
        }
        $db = connect_db();
        $existing = Event::find($db, $id);
        if ($existing && (int) $existing['organiser_id'] === $user['id']) {
            if (!empty($existing['image'])) {
                $path = UPLOAD_PATH . '/' . $existing['image'];
                if (is_file($path)) {
                    @unlink($path);
                }
            }
            Event::deleteByOrganiser($db, $id, $user['id']);
            flash('success', 'Event deleted.');
        } else {
            flash('error', 'Could not delete event.');
        }
        redirect(url('event', 'mine'));
    }
}