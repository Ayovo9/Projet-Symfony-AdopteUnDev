<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'required' => false,
            ])
            ->add('contractType', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'Tous' => '',
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Freelance' => 'Freelance',
                    'Stage' => 'Stage',
                    'Alternance' => 'Alternance'
                ],
                'required' => false,
            ])
            ->add('minSalary', NumberType::class, [
                'label' => 'Salaire minimum',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Salaire minimum'
                ]
            ])
            ->add('programmingLanguages', ChoiceType::class, [
                'label' => 'Langages de programmation',
                'choices' => [
                    'PHP' => 'PHP',
                    'JavaScript' => 'JavaScript',
                    'Python' => 'Python',
                    'Java' => 'Java',
                    'C#' => 'C#',
                    'Ruby' => 'Ruby',
                    'Go' => 'Go',
                    'Swift' => 'Swift',
                    'TypeScript' => 'TypeScript',
                    'Kotlin' => 'Kotlin'
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('experienceLevel', ChoiceType::class, [
                'label' => 'Niveau d\'expérience',
                'choices' => [
                    'Tous' => '',
                    'Junior (1-2 ans)' => 1,
                    'Intermédiaire (2-4 ans)' => 2,
                    'Senior (5+ ans)' => 3
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
