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

    
}
