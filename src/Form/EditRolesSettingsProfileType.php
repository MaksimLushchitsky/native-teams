<?php

namespace App\Form;

use App\Entity\Roles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EditRolesSettingsProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class,  ['label' => false, 'required' => false])
            ->add('phone', TextType::class,  ['label' => false, 'required' => false])
            ->add('basic_salary', TextType::class,  ['label' => false, 'required' => false])
            ->add('start_date', DateType::class,  ['label' => false, 'required' => false])
            ->add('end_date', DateType::class,  ['label' => false, 'required' => false])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->add('user', EditUserSettingsProfileType::class,  ['label' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Roles::class,
        ]);
    }
}
