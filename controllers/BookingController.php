<?php

declare(strict_types=1);

class BookingController extends BaseController
{
    public function history(): void
    {
        require_role('attendee', 'organiser');
        $user = current_user();
        $db = connect_db();
        $bookings = Booking::forAttendee($db, $user['id']);
        $this->view('booking/history', ['bookings' => $bookings]);
    }

    public function store(): void
    {
        require_role('attendee', 'organiser');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('event', 'index'));
        }
        $user = current_user();
        $eventId = (int) ($_POST['event_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $priceCheck = (float) ($_POST['ticket_price'] ?? -1);

        if ($eventId < 1 || $quantity < 1) {
            flash('error', 'Invalid booking.');
            redirect(url('event', 'detail', ['id' => $eventId]));
        }

        $db = connect_db();
        $event = Event::find($db, $eventId);
        if (!$event) {
            flash('error', 'Event not found.');
            redirect(url('event', 'index'));
        }

        $code = Booking::createWithSeatUpdate($db, $user['id'], $eventId, $quantity, $priceCheck);
        if ($code === null) {
            flash('error', 'Booking failed. Check seat availability and price.');
            redirect(url('event', 'detail', ['id' => $eventId]));
        }
        flash('success', 'Booking confirmed! Your booking code: ' . $code);
        redirect(url('booking', 'history'));
    }
}