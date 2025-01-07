<?php

namespace App\Form;

use App\Entity\DeveloperProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DeveloperProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio',
                'required' => false
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation'
            ])
            ->add('experienceLevel', ChoiceType::class, [
                'label' => 'Niveau d\'expérience',
                'choices' => [
                    'Junior (1-2 ans)' => 1,
                    'Intermédiaire (2-4 ans)' => 2,
                    'Senior (5+ ans)' => 3
                ]
            ])
            ->add('preferredContractType', ChoiceType::class, [
                'label' => 'Type de contrat souhaité',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Freelance' => 'Freelance',
                    'Stage' => 'Stage',
                    'Alternance' => 'Alternance'
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
                'expanded' => true
            ])
            ->add('github', UrlType::class, [
                'label' => 'Profil GitHub',
                'required' => false
            ])
            ->add('linkedin', UrlType::class, [
                'label' => 'Profil LinkedIn',
                'required' => false
            ])
            ->add('minimumSalary', MoneyType::class, [
                'label' => 'Salaire annuel minimum souhaité',
                'required' => false,
                'currency' => 'EUR',
                'divisor' => 1,
                'help' => 'Indiquez votre salaire annuel minimum souhaité en euros (brut)'
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG ou PNG)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeveloperProfile::class,
        ]);
    }
}
