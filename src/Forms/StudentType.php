<?php

namespace App\Forms;

use App\Entity\Student;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => false, 'attr' => ['autocomplete' => 'off']])
	        ->add('age', IntegerType::class, ['attr' => ['autocomplete' => 'off', 'min' => 6, 'max' => '20']])
	        ->add('sex', ChoiceType::class, ['choices' => ['Man' => 'man', 'Woman' => 'woman']])
	        ->add('phone', TextType::class, ['attr' => ['autocomplete' => 'off']])
	        ->add('save', SubmitType::class, ['attr' => ['class' => 'btn-success']]);
    }

    // Dont forget to activate csrf

	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => Student::class,
            'csrf_protection' => false
	    ));
	}
}