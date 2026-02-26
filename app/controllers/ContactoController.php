<?php

class ContactoController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Contacto — ' . SITE_NAME,
            'sectionName' => 'Contacto',
        ]);
    }
}
