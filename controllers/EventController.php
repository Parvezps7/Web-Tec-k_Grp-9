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

       
}
