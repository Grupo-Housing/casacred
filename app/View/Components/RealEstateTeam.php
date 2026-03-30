<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RealEstateTeam extends Component
{
    public $teamMembers;
    public $sectionTitle;
    public $sectionSubtitle;
    public $learnMoreText;
    public $learnMoreLink;

    public function __construct(
        $sectionTitle = 'Conoce a nuestro',
        $sectionSubtitle = 'equipo inmobiliario',
        $learnMoreText = 'Conoce más sobre nosotros',
        $learnMoreLink = '/nosotros'
    ) {
        $this->sectionTitle = $sectionTitle;
        $this->sectionSubtitle = $sectionSubtitle;
        $this->learnMoreText = $learnMoreText;
        $this->learnMoreLink = $learnMoreLink;

        $this->teamMembers = [
            [
                'id' => 1,
                'name' => 'Karen Salas',
                'title' => 'Gestora',
                'image' => 'karen-salas.webp',
                'email' => 'ana@inmobiliaria.com',
                'phone' => '+593 99 345 6789',
                'specialties' => ['Rentas', 'Propiedades de Lujo'],
            ],
            [
                'id' => 2,
                'name' => 'Veronica Medina',
                'title' => 'Asesora',
                'image' => 'veronica-medina.webp',
                'email' => 'ana@inmobiliaria.com',
                'phone' => '+593 99 345 6789',
                'specialties' => ['Rentas', 'Propiedades de Lujo'],
            ],
            [
                'id' => 3,
                'name' => 'Anael Mosquera',
                'title' => 'Gestora',
                'image' => 'anael-mosquera.webp',
                'email' => 'ana@inmobiliaria.com',
                'phone' => '+593 99 345 6789',
                'specialties' => ['Rentas', 'Propiedades de Lujo'],
            ]
        ];
    }

    public function render()
    {
        return view('components.real-estate-team');
    }
}
