<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Genre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GenreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Genre::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Genre')
            ->setEntityLabelInPlural('Genres musicaux')
            ->setPageTitle(Crud::PAGE_INDEX, 'Genres musicaux')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un genre')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier un genre')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name', 'Nom');
        yield TextField::new('slug', 'Slug')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm()->hideWhenCreating();
        yield DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm();
    }
}
