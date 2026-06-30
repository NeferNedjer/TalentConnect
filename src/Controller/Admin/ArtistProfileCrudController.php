<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ArtistProfile;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArtistProfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArtistProfile::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Profil artiste')
            ->setEntityLabelInPlural('Profils artistes')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('stageName', 'Nom de scène');
        yield ChoiceField::new('artistType', 'Type')
            ->setChoices([
                'Artiste solo' => 'solo',
                'Groupe' => 'groupe',
            ]);
        yield TextField::new('city', 'Ville');
        yield TextField::new('country', 'Pays');
        yield IntegerField::new('profileCompletion', 'Complétion (%)');
        yield ChoiceField::new('verificationStatus', 'Statut de vérification')
            ->setChoices([
                'En attente' => 'pending',
                'Vérifié' => 'verified',
                'Rejeté' => 'rejected',
            ]);
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
    }
}
