<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Unique;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom' )
            ->add('prenom')
            ->add('pseudo')
            ->add('mail')
            ->add('telephone')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'attr'=>[
                    'class'=>'radio'
                ],
                'choice_label' => 'nom'
            ])


            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [ 'label' => 'Mot de passe : ',
                    'hash_property_path' =>'password'
                ],
                'second_options' => ['label' => 'Confirmez le mot de passe',
                    'hash_property_path' => 'password'


                ],
                'mapped' => false,
                'constraints' => [
                    new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', 'Merci de respecter le format du mot de passe')
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('inscription', SubmitType::class,[
                'attr'=>[
                    'type'=>'button'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
