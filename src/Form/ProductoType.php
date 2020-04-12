<?php

namespace App\Form;

use App\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('precio')
            ->add('usomedicoIdusomedico')
            ->add('ficheroimagen', FileType::class, [
                'label' => 'Fichero de producto',
                'mapped' => false,
                'constraints' => [new File(['mimeTypes' =>
                ['image/png', 'image/jpeg', 'image/gif'], 'mimeTypesMessage' =>
                'Solo se permiten imágenes'])]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
