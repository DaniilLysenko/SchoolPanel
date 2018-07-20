<?php

namespace App\Forms;

use App\Entity\Student;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadImageType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', FileType::class, ['mapped' => false])
            ->add('id', HiddenType::class, ['mapped' => false])
            ->add('upload', SubmitType::class, ['attr' => ['class' => 'btn-success']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Student::class
        ));
    }
}