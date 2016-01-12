<?php namespace Halo;

class bookings extends Controller
{

    function index()
    {
        $this->bookings = get_all("SELECT * FROM bookings");
    }

    function view()
    {
        $booking_id = $this->params[0];
        $this->booking = get_first("SELECT * FROM bookings WHERE booking_id = '{$booking_id}'");
    }

    function edit()
    {
        $booking_id = $this->params[0];
        $this->booking = get_first("SELECT * FROM bookings WHERE booking_id = '{$booking_id}'");
    }

    function post_edit()
    {
        $data = $_POST['data'];
        insert('booking', $data);
    }

    function ajax_delete()
    {
        exit(q("DELETE FROM bookings WHERE booking_id = '{$_POST['booking_id']}'") ? 'Ok' : 'Fail');
    }

}