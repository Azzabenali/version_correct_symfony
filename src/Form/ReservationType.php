<?php

namespace App\Form;

use App\Entity\event;
use App\Entity\Reservation;
use App\Entity\ticket;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('resdate')
            ->add('numbertickets')
            ->add('totalprice')
            ->add('user', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'id',
            ])
            ->add('ticket', EntityType::class, [
                'class' => ticket::class,
                'choice_label' => 'id',
            ])
            ->add('event', EntityType::class, [
                'class' => event::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
