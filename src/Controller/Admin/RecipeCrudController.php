<?php

// namespace App\Controller\Admin;

// use App\Entity\Recipe;
// use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

// class RecipeCrudController extends AbstractCrudController
// {
//     public static function getEntityFqcn(): string
//     {
//         return Recipe::class;
//     }

//     /*
//     public function configureFields(string $pageName): iterable
//     {
//         return [
//             IdField::new('id'),
//             TextField::new('title'),
//             TextEditorField::new('description'),
//         ];
//     }
//     */
// }



namespace App\Controller\Admin;

use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FileField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RecipeCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Recette')
            ->setEntityLabelInSingular('Recette')

            ->setPageTitle("index", "Site Recette - Administration des Recettes")

            ->setPaginatorPageSize(10);
    }


    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            TextField::new('ImageName'),
            ImageField::new('imageName', 'Image')
                ->setBasePath('/images/recette')
                ->setLabel('Image')
                ->onlyOnIndex(),
            TextField::new('imageFile', 'Image')
                ->setFormType(VichFileType::class)
                ->setFormTypeOptions(['allow_delete' => false])
                ->setLabel('Image')
                ->onlyOnForms(),
            TextareaField::new('Description')
            ->onlyOnForms(),
            MoneyField::new('price')->setCurrency('EUR'),
            BooleanField::new('isFavorite'),
            BooleanField::new('isPublic'),
            DateTimeField::new('createdAt')
                ->hideOnForm()
        ];
    }

}
