<?php

class EventoController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Eventos — ' . SITE_NAME,
            'sectionName' => 'Eventos y Festividades',
        ]);
    }
}
