<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProfessionalProfile;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProfessionalProfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProfessionalProfile::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Profil professionnel')
            ->setEntityLabelInPlural('Profils professionnels')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('companyName', 'Entreprise');
        yield ChoiceField::new('type', 'Type')
            ->setChoices([
                'Label' => 'label',
                'Studio' => 'studio',
                'Producteur' => 'producteur',
                'Manager' => 'manager',
                'Festival' => 'festival',
                'Salle' => 'salle',
                'Organisateur' => 'organisateur',
                'Autre' => 'other',
            ]);
        yield TextField::new('city', 'Ville');
        yield TextField::new('country', 'Pays');
        yield ChoiceField::new('verificationStatus', 'Statut de vérification')
            ->setChoices([
                'En attente' => 'pending',
                'Vérifié' => 'verified',
                'Rejeté' => 'rejected',
            ]);
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
    }
}
