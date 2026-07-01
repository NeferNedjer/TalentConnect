<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Announcement;
use App\Entity\Genre;
use App\Enum\AnnouncementType as AnnouncementTypeEnum;
use App\Enum\RemunerationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTimeImmutable('today');
        $maxExpiresAt = $today->modify('+365 days');

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'empty_data' => '',
                'attr' => ['placeholder' => 'Ex. Chanteur·se recherché·e pour projet live'],
                'constraints' => [
                    new NotBlank(message: 'Le titre est obligatoire.'),
                    new Length(max: 150),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'empty_data' => '',
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Décrivez le projet, le contexte, le profil recherché et les attentes…',
                ],
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire.'),
                ],
            ])
            ->add('announcementType', EnumType::class, [
                'class' => AnnouncementTypeEnum::class,
                'label' => 'Type d\'annonce',
                'choice_label' => static fn (AnnouncementTypeEnum $type): string => match ($type) {
                    AnnouncementTypeEnum::SeekingMusician => 'Recherche musicien',
                    AnnouncementTypeEnum::SeekingGroup => 'Recherche groupe',
                    AnnouncementTypeEnum::SeekingCollaboration => 'Collaboration',
                    AnnouncementTypeEnum::SeekingTeacher => 'Recherche professeur',
                    AnnouncementTypeEnum::SeekingStudent => 'Recherche élève',
                    AnnouncementTypeEnum::Other => 'Autre',
                },
                'constraints' => [
                    new NotBlank(message: 'Le type d\'annonce est obligatoire.'),
                ],
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Genres musicaux',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => ['placeholder' => 'Lyon'],
                'constraints' => [new Length(max: 120)],
            ])
            ->add('region', TextType::class, [
                'label' => 'Région',
                'required' => false,
                'attr' => ['placeholder' => 'Auvergne-Rhône-Alpes'],
                'constraints' => [new Length(max: 120)],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => false,
                'attr' => ['placeholder' => 'France'],
                'constraints' => [new Length(max: 120)],
            ])
            ->add('isRemote', CheckboxType::class, [
                'label' => 'Cette annonce peut se faire à distance',
                'required' => false,
            ])
            ->add('remunerationType', EnumType::class, [
                'class' => RemunerationType::class,
                'label' => 'Type de rémunération',
                'choice_label' => static fn (RemunerationType $type): string => match ($type) {
                    RemunerationType::Unpaid => 'Non rémunéré',
                    RemunerationType::Negotiable => 'Négociable',
                    RemunerationType::Fixed => 'Montant fixe',
                    RemunerationType::ToDiscuss => 'À discuter',
                },
                'constraints' => [
                    new NotBlank(message: 'Le type de rémunération est obligatoire.'),
                ],
            ])
            ->add('remunerationAmount', NumberType::class, [
                'label' => 'Montant (€)',
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'placeholder' => 'Ex. 500',
                    'min' => '0',
                    'step' => '0.01',
                ],
            ])
            ->add('expiresAt', DateType::class, [
                'label' => 'Date d\'expiration',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => [
                    'min' => $today->format('Y-m-d'),
                    'max' => $maxExpiresAt->format('Y-m-d'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual(
                        value: 'today',
                        message: 'La date d\'expiration ne peut pas être dans le passé.',
                    ),
                    new LessThanOrEqual(
                        value: $maxExpiresAt,
                        message: 'La date d\'expiration ne peut pas dépasser 365 jours à partir d\'aujourd\'hui.',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announcement::class,
            'constraints' => [
                new Callback([self::class, 'validateFixedRemunerationAmount']),
            ],
        ]);
    }

    public static function validateFixedRemunerationAmount(Announcement $announcement, ExecutionContextInterface $context): void
    {
        if ($announcement->getRemunerationType() !== RemunerationType::Fixed) {
            return;
        }

        $amount = $announcement->getRemunerationAmount();
        if ($amount === null || $amount === '') {
            $context->buildViolation('Le montant est obligatoire pour une rémunération à montant fixe.')
                ->atPath('remunerationAmount')
                ->addViolation();

            return;
        }

        if ((float) $amount <= 0) {
            $context->buildViolation('Le montant doit être supérieur à zéro.')
                ->atPath('remunerationAmount')
                ->addViolation();
        }
    }
}
