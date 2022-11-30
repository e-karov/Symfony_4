<?php
namespace App\Form;

use App\Entity\Album;
use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormBuilderInterface;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "attr" => ["class"=>"form-control mb-2"]
                ])
            ->add('description', TextareaType::class, [
                "attr" => ["class"=>"form-control mb-2"]
                ])
            ->add('artist', TextType::class, [
                "attr" => ["class"=>"form-control mb-2"]
                ])
            ->add('releaseYear', DateType::class, [
                "years"=>range(1960,2024),
                "label"=>"Release Date",
                "attr"=>["class"=>"form-control mb-2"]
            ])
            ->add('genre', TextType::class, [
                "attr" => ["class"=>"form-control mb-2"]
                ])
    
                ->add('fk_rating', EntityType::class, [
                "label"=>"Rating",
                "class"=>Rating::class,
                "choice_label"=>"name",
                "attr"=>["class"=>"form-control mb-2" ],
                ])
            ->add('picture', FileType::class, [
                'label'=>'Upload Picture',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File([
                    'maxSize'=>'1024k',
                    'mimeTypes'=> [
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                    ],
                    'mimeTypesMessage'=>'Please upload a valid image file.',
                ])
                ],
                'attr'=> ['class'=>'form-control mb-2'],
            ])

            ->add('save', SubmitType::class, [
                "attr" => ["class"=>"btn btn-primary", "style"=>"margin-bottom:15px"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'=>Album::class
        ]);
    }
}