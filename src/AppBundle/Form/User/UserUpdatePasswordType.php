<?php

namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\EventListener\AntiSqlInjectionFormListener;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserUpdatePasswordType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        // Using $option to avoid Codacy error "Unused Code"
        $option = null;

        // The entity fields are added to our form.
        $builder
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Length(['max' => 4096]),
                    new Regex([
                        'pattern' => '/^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/',
                        'message' => 'Le mot de passe doit comporter au moins 6 caractères,
                        minuscule, majuscule et numérique.',
                    ]),
                    new NotBlank(),
                ],
            ])
            ->addEventSubscriber(new AntiSqlInjectionFormListener());
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
