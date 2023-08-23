<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('pseudo')
            ->add('telephone')
            ->add('mail')
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>'nom'
            ])


            ->add('plainPassword', RepeatedType::class, [
                'required' =>false,
                'type' => PasswordType::class,
                'first_options' => [ 'label' => 'Mot de passe : ',
                    'hash_property_path' =>'password'
                ],
                'second_options' => [ 'label' => 'Confirmez le mot de passe',
                    'hash_property_path' =>'password'

                ],
                'mapped' => false,
                'constraints' =>[
                    new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/','Merci de respecter le format du mot de passe')
                ]
            ])
            ->add('save', SubmitType::class, [
                'label'=>'Enregistrer les modifications'
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
