<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType; // <-- ajouté ici

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('resdate')
            ->add('numbertickets')
            ->add('totalprice')
            ->add('titre', TextType::class, [
                'label' => 'Titre de la réservation',
                'required' => true,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('tickets', EntityType::class, [
                'class' => Ticket::class,
                'choice_label' => function(Ticket $ticket) {
                    return $ticket->getName() . ' - ' . ($ticket->getEvent() ? $ticket->getEvent()->getTitre() : 'Événement inconnu');
                },
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'titre', // mieux que 'id' pour l’affichage
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
