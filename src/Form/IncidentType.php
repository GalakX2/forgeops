<?php

namespace App\Form;

use App\Entity\Incident;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncidentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('status', ChoiceType::class, [
                'choices' => ['Open' => 'open', 'Monitoring' => 'monitoring', 'Resolved' => 'resolved'],
                'label' => 'Statut'
            ])
            ->add('severity', ChoiceType::class, [
                'choices' => ['Critique (Sev1)' => 'sev1', 'Majeur (Sev2)' => 'sev2', 'Mineur (Sev3)' => 'sev3'],
                'label' => 'Sévérité'
            ])
            ->add('startedAt', DateTimeType::class, [
                'widget' => 'single_text', 
                'label' => 'Début incident'
            ])
            ->add('resolvedAt', DateTimeType::class, [
                'widget' => 'single_text', 
                'required' => false, 
                'label' => 'Résolu le (optionnel)'
            ])
            ->add('summary', TextareaType::class, ['label' => 'Résumé'])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Affiche des checkboxes (plus clair)
                'label' => 'Services impactés'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Incident::class]);
    }
}