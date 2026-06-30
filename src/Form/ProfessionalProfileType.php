<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ProfessionalProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class ProfessionalProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $urlConstraints = [
            new Url(message: 'Veuillez entrer une URL valide.', requireTld: false),
        ];

        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'constraints' => [
                    new NotBlank(message: 'Le nom de l\'entreprise est obligatoire.'),
                    new Length(max: 150),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'empty_data' => null,
                'default_protocol' => 'https',
                'constraints' => $urlConstraints,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de professionnel',
                'choices' => [
                    'Label' => 'label',
                    'Studio' => 'studio',
                    'Producteur' => 'producteur',
                    'Manager' => 'manager',
                    'Festival' => 'festival',
                    'Salle' => 'salle',
                    'Organisateur' => 'organisateur',
                    'Autre' => 'other',
                ],
            ])
            ->add('emailPublic', EmailType::class, [
                'label' => 'E-mail public',
                'required' => false,
                'constraints' => [
                    new Email(message: 'Veuillez entrer une adresse e-mail valide.'),
                    new Length(max: 180),
                ],
            ])
            ->add('telephonePublic', TelType::class, [
                'label' => 'Téléphone public',
                'required' => false,
                'constraints' => [new Length(max: 30)],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'constraints' => [new Length(max: 120)],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'constraints' => [new Length(max: 20)],
            ])
            ->add('region', TextType::class, [
                'label' => 'Région',
                'required' => false,
                'constraints' => [new Length(max: 120)],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => false,
                'constraints' => [new Length(max: 120)],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfessionalProfile::class,
        ]);
    }
}
