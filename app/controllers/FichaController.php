<?php

class FichaController extends Controller
{
    public function show(string $slug): void
    {
        $this->render('public/placeholder', [
            'pageTitle'   => ucfirst($slug) . ' — ' . SITE_NAME,
            'sectionName' => 'Atractivo: ' . $slug,
        ]);
    }
}
