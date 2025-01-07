<?php

namespace App\Form;

use App\Entity\JobPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class JobPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du poste',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le titre du poste',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du poste',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description du poste',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer la localisation du poste',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('contractType', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Stage' => 'Stage',
                    'Alternance' => 'Alternance',
                    'Freelance' => 'Freelance'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un type de contrat',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('experienceLevel', ChoiceType::class, [
                'label' => 'Niveau d\'expérience requis',
                'choices' => [
                    'Débutant' => 1,
                    'Junior (1-3 ans)' => 2,
                    'Intermédiaire (3-5 ans)' => 3,
                    'Senior (5-8 ans)' => 4,
                    'Expert (8+ ans)' => 5
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un niveau d\'expérience',
                    ]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('salary', IntegerType::class, [
                'label' => 'Salaire annuel (€)',
                'required' => false,
                'constraints' => [
                    new Range([
                        'min' => 20000,
                        'max' => 200000,
                        'notInRangeMessage' => 'Le salaire doit être compris entre {{ min }}€ et {{ max }}€',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 20000,
                    'max' => 200000,
                    'placeholder' => 'Ex : 45000'
                ]
            ])
            ->add('programmingLanguages', ChoiceType::class, [
                'label' => 'Langages de programmation requis',
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
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner au moins un langage de programmation',
                    ]),
                ],
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JobPost::class,
        ]);
    }
}
