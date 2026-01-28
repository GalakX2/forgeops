<?php

namespace App\Form;

use App\Entity\Journal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Détection' => 'detected', 'Investigation' => 'investigation',
                    'Surveillance' => 'monitoring', 'Rétabli' => 'recovered', 'Post-Mortem' => 'postmortem'
                ]
            ])
            ->add('message', TextareaType::class)
            ->add('occurredAt', DateTimeType::class, ['widget' => 'single_text', 'label' => 'Horodatage'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Journal::class]);
    }
}