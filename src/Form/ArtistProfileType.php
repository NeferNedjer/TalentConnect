<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ArtistProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class ArtistProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $urlConstraints = [
            new Url(message: 'Veuillez entrer une URL valide.', requireTld: false),
        ];

        $builder
            ->add('stageName', TextType::class, [
                'label' => 'Nom de scène',
                'constraints' => [
                    new NotBlank(message: 'Le nom de scène est obligatoire.'),
                    new Length(max: 120),
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Biographie',
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
            ->add('artistType', ChoiceType::class, [
                'label' => 'Type d\'artiste',
                'choices' => [
                    'Artiste solo' => 'solo',
                    'Groupe' => 'groupe',
                ],
            ])
            ->add('spotifyUrl', UrlType::class, [
                'label' => 'Spotify',
                'required' => false,
                'empty_data' => null,
                'default_protocol' => 'https',
                'constraints' => $urlConstraints,
            ])
            ->add('youtubeUrl', UrlType::class, [
                'label' => 'YouTube',
                'required' => false,
                'empty_data' => null,
                'default_protocol' => 'https',
                'constraints' => $urlConstraints,
            ])
            ->add('instagramUrl', UrlType::class, [
                'label' => 'Instagram',
                'required' => false,
                'empty_data' => null,
                'default_protocol' => 'https',
                'constraints' => $urlConstraints,
            ])
            ->add('facebookUrl', UrlType::class, [
                'label' => 'Facebook',
                'required' => false,
                'empty_data' => null,
                'default_protocol' => 'https',
                'constraints' => $urlConstraints,
            ])
            ->add('tiktokUrl', UrlType::class, [
                'label' => 'TikTok',
                'required' => false,
                'empty_data' => null,
                'default_protocol' => 'https',
                'constraints' => $urlConstraints,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArtistProfile::class,
        ]);
    }
}
