<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('public/home', [
            'pageTitle' => SITE_NAME . ' — Guia turistica de Purranque',
        ]);
    }
}
