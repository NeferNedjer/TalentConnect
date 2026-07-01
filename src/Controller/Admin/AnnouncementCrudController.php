<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Announcement;
use App\Enum\AnnouncementStatus;
use App\Enum\AnnouncementType;
use App\Enum\RemunerationType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class AnnouncementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Announcement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Annonce')
            ->setEntityLabelInPlural('Annonces')
            ->setPageTitle(Crud::PAGE_INDEX, 'Annonces')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une annonce')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier une annonce')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['title', 'description', 'city']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices(self::statusChoices()))
            ->add(ChoiceFilter::new('announcementType', 'Type')->setChoices(self::announcementTypeChoices()))
            ->add('city')
            ->add(EntityFilter::new('publisherArtist', 'Profil artiste'))
            ->add(EntityFilter::new('publisherProfessional', 'Profil professionnel'))
            ->add(EntityFilter::new('createdBy', 'Créé par'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('slug', 'Slug')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm();
        yield DateTimeField::new('deletedAt', 'Supprimé le')->hideOnForm();

        yield FormField::addFieldset('Informations générales', 'fa fa-circle-info');
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield ChoiceField::new('announcementType', 'Type d\'annonce')
            ->setChoices(self::announcementTypeChoices());
        yield ChoiceField::new('status', 'Statut')
            ->setChoices(self::statusChoices())
            ->renderAsBadges();

        yield FormField::addFieldset('Publication', 'fa fa-user');
        yield AssociationField::new('publisherArtist', 'Profil artiste éditeur');
        yield AssociationField::new('publisherProfessional', 'Profil professionnel éditeur');

        yield AssociationField::new('createdBy', 'Créé par')->hideOnForm();

        yield FormField::addFieldset('Genres', 'fa fa-music');
        yield AssociationField::new('genres', 'Genres musicaux')
            ->autocomplete()
            ->hideOnIndex();

        yield FormField::addFieldset('Localisation', 'fa fa-location-dot');
        yield TextField::new('city', 'Ville');
        yield TextField::new('region', 'Région')->hideOnIndex();
        yield TextField::new('country', 'Pays')->hideOnIndex();
        yield BooleanField::new('isRemote', 'À distance')->hideOnIndex();

        yield FormField::addFieldset('Rémunération', 'fa fa-coins');
        yield ChoiceField::new('remunerationType', 'Type de rémunération')
            ->setChoices(self::remunerationTypeChoices())
            ->hideOnIndex();
        yield NumberField::new('remunerationAmount', 'Montant')->hideOnIndex();
        yield TextField::new('currency', 'Devise')->hideOnIndex();

        yield FormField::addFieldset('Dates', 'fa fa-calendar');
        yield DateTimeField::new('publishedAt', 'Date de publication');
        yield DateTimeField::new('expiresAt', 'Date d\'expiration')->hideOnIndex();
        yield DateTimeField::new('closedAt', 'Date de clôture')->hideOnIndex();
    }

    /**
     * @return array<string, AnnouncementStatus>
     */
    private static function statusChoices(): array
    {
        return [
            'Brouillon' => AnnouncementStatus::Draft,
            'Publiée' => AnnouncementStatus::Published,
            'Clôturée' => AnnouncementStatus::Closed,
            'Archivée' => AnnouncementStatus::Archived,
        ];
    }

    /**
     * @return array<string, AnnouncementType>
     */
    private static function announcementTypeChoices(): array
    {
        return [
            'Recherche musicien' => AnnouncementType::SeekingMusician,
            'Recherche groupe' => AnnouncementType::SeekingGroup,
            'Collaboration' => AnnouncementType::SeekingCollaboration,
            'Recherche professeur' => AnnouncementType::SeekingTeacher,
            'Recherche élève' => AnnouncementType::SeekingStudent,
            'Autre' => AnnouncementType::Other,
        ];
    }

    /**
     * @return array<string, RemunerationType>
     */
    private static function remunerationTypeChoices(): array
    {
        return [
            'Non rémunéré' => RemunerationType::Unpaid,
            'Négociable' => RemunerationType::Negotiable,
            'Montant fixe' => RemunerationType::Fixed,
            'À discuter' => RemunerationType::ToDiscuss,
        ];
    }
}
