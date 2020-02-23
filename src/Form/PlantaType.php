<?php

namespace App\Form;

use App\Entity\Planta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class PlantaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('localizacion')
            ->add('ficheroimagen', FileType::class, [
                'label' => 'Fichero de planta',
                'mapped' => false,
                'constraints' => [new File(['mimeTypes' =>
                ['image/png', 'image/jpeg', 'image/gif'], 'mimeTypesMessage' =>
                'Solo se permiten imágenes'])]
            ])
            ->add('colorflorIdcolorflor')
            ->add('parteutilIdparteutil')
            ->add('usomedicoIdusomedico');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planta::class,
        ]);
    }
}
