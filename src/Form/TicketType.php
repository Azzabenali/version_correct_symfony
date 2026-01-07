<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Reservation;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('name')
    ->add('price')
    ->add('quantity', IntegerType::class, [
        'label' => 'Nombre de places',
        'required' => true,
        'attr' => ['min' => 1],
    ])
    ->add('reservation', EntityType::class, [
        'class' => Reservation::class,
        'choice_label' => function(Reservation $res) {
            $eventName = $res->getEvent() ? $res->getEvent()->getTitre() : 'Événement inconnu';
            return 'ID '.$res->getId().' - '.$eventName;
        },
        'placeholder' => 'Choisir une réservation',
        'required' => true,
    ])
    ->add('event', EntityType::class, [
        'class' => Event::class,
        'choice_label' => function(Event $event) {
            return $event->getTitre();
        },
        'placeholder' => 'Choisir un événement',
        'required' => true,
    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
