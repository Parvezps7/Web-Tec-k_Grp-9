<?php

declare(strict_types=1);

class AdminController extends BaseController
{
    public function users(): void
    {
        require_role('admin');
        $db = connect_db();
        $users = User::all($db);
        $this->view('admin/users', ['users' => $users]);
    }

    public function events(): void
    {
        require_role('admin');
        $db = connect_db();
        $events = Event::allForAdmin($db);
        $this->view('admin/events', ['events' => $events]);
    }

    public function deleteuser(): void
    {
        require_role('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('admin', 'users'));
        }
        $id = (int) ($_POST['id'] ?? 0);
        $self = current_user();
        if ($id < 1 || $id === $self['id']) {
            flash('error', 'Cannot delete this user.');
            redirect(url('admin', 'users'));
        }
        $db = connect_db();
        User::delete($db, $id);
        flash('success', 'User deleted.');
        redirect(url('admin', 'users'));
    }

    public function deleteevent(): void
    {
        require_role('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('admin', 'events'));
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id < 1) {
            redirect(url('admin', 'events'));
        }
        $db = connect_db();
        $existing = Event::find($db, $id);
        if ($existing && !empty($existing['image'])) {
            $path = UPLOAD_PATH . '/' . $existing['image'];
            if (is_file($path)) {
                @unlink($path);
            }
        }
        Event::deleteById($db, $id);
        flash('success', 'Event deleted.');
        redirect(url('admin', 'events'));
    }
}
