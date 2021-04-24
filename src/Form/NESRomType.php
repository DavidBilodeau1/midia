<?php

namespace App\Form;

use App\Entity\NESRom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NESRomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('information', FormType::class, [ 'required' => false, 'inherit_data' => true, 'label_attr' => ['class' => 'card-title']])
            ->add('name', TextType::class, ['required' => false, 'row_attr' => ['class' => 'form-group'], 'attr' => ['class' => 'form-control col']])
            ->add('year', TextType::class, ['required' => false, 'row_attr' => ['class' => 'form-group'], 'attr' => ['class' => 'form-control col']])
            ->add('path', TextType::class, ['required' => true, 'row_attr' => ['class' => 'form-group'], 'attr' => ['class' => 'form-control col']])
            ->add('players', NumberType::class, ['required' => false, 'row_attr' => ['class' => 'form-group'], 'attr' => ['class' => 'form-control col']])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn-primary btn']])
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NESRom::class,
        ]);
    }
}
