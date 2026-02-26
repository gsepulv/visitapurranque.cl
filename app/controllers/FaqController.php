<?php

class FaqController extends Controller
{
    public function index(): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => 'Preguntas Frecuentes — ' . SITE_NAME,
            'sectionName' => 'Preguntas Frecuentes',
        ]);
    }
}
