<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppressionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, array(
                'widget' => 'single_text'
            ))
            ->add('duree')
            ->add('dateLimiteInscription', DateTimeType::class, array(
                'widget'=>'single_text'
            ))
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'expanded'  => true
                //TODO mettre en liste déroulante
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'expanded'  => true
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => 'libelle',
            ])
            //->add('participants', EntityType::class, [
            //       'class' => Participant::class,
            //       'choice_label' => 'pseudo'
            //])
            //TODO ajouter la liste de participants dans entité Sortie?
            ->add('organisateur', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'pseudo'
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
