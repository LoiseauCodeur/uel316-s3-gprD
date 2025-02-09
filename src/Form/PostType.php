<?php 

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Importer le type FileType

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, [
                'required' => false
            ])
            ->add('imageFile', FileType::class, [
                'required' => false,  
                'mapped' => false,    
                'label' => 'Upload Image',
            ])
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
                'label' => 'Submit'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
