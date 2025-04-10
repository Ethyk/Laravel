<!-- src/Pages/Salons/Index.svelte (ou le nom de ta page) -->
<script lang="ts">
    import GenericModal from '@/components/GenericModal.svelte'; // Chemin vers ton nouveau modal générique
    import { useForm, page } from '@inertiajs/svelte';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { toast } from 'svelte-sonner';
    import { Button } from "@/components/ui/button/index.js";

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Salons', href: '/salons' }, // Ajout du fil d'ariane pour Salons
    ];

    let isModalOpen = $state(false);
    let selectedItem = $state(null); // Pour savoir si on édite ou crée

    // Props reçues de Laravel/Inertia
    let { csrf_token, salons, last, flash } = $props();

    // Définition du formulaire spécifique aux Salons
    const form = useForm({
        id: null,
        name: '',
        description: '',
        adresse: '',
        ville: '',
        code_postal: '',
        pays: '',
        _token: csrf_token // Initialise le token CSRF
    });

    let isEdit = $derived(!!selectedItem); // Détermine si c'est une édition

    // Fonction pour ouvrir le modal (création ou édition)
    const openModal = (salon = null) => {
        selectedItem = salon;
        $form.reset(); // Réinitialise le formulaire
        $form.clearErrors(); // Efface les erreurs précédentes

        if (salon) {
            // Mode édition: pré-remplir le formulaire
            $form.id = salon.id;
            $form.name = salon.name;
            $form.description = salon.description;
            $form.adresse = salon.adresse;
            $form.ville = salon.ville;
            $form.code_postal = salon.code_postal;
            $form.pays = salon.pays;
        }
        // Assure que le token est toujours à jour (peut être utile si la page reste ouverte longtemps)
        $form._token = csrf_token;
        isModalOpen = true; // Ouvre le modal
    };

    // Fonction pour gérer la soumission du formulaire (logique métier ici)
    const handleSubmit = () => {
        const options = {
            preserveScroll: true,
            onSuccess: (pageData: any) => { // Utilise pageData pour accéder aux props flash mises à jour
                const currentFlash = pageData.props.flash; // Accède au flash de la réponse
                if (isEdit) {
                    toast.success(currentFlash.success || 'Salon mis à jour avec succès.');
                } else {
                    // Dans Inertia, la prop 'last' devrait être mise à jour dans la réponse du POST
                    // Assure-toi que ton contrôleur retourne bien l'objet créé dans les props
                    const newlyCreatedSalon = pageData.props.last; // Suppose que 'last' contient le dernier salon créé

                    toast(currentFlash.success || 'Salon créé avec succès.', {
                        action: {
                            label: 'Annuler',
                            // Utilise l'ID retourné par le serveur
                            onClick: () => $form.delete(`/salons/${newlyCreatedSalon?.id}`, {
                                onSuccess: (undoPageData: any) => {
                                    toast.success(undoPageData.props.flash.success || 'Création annulée.');
                                },
                                onError: () => toast.error('Erreur lors de l\'annulation.')
                            }),
                        },
                    });
                }
                $form.reset(); // Réinitialise après succès
                isModalOpen = false; // Ferme le modal
            },
            onError: (errors: any) => {
                // Le flash d'erreur est généralement géré globalement par Inertia,
                // mais on peut afficher un toast générique ou spécifique si besoin.
                toast.error(flash?.error || 'Une erreur est survenue. Veuillez vérifier les champs.');
                // Optionnel: focus sur le premier champ en erreur
                focusOnErrorField();
            }
        };

        if (isEdit) {
            $form.patch(`/salons/${$form.id}`, options);
        } else {
            $form.post(`/salons`, options);
        }
    };

    // Fonction pour gérer l'annulation depuis le modal
    const handleCancel = () => {
        $form.clearErrors();
        $form.reset(); // Optionnel: réinitialiser le formulaire en cas d'annulation
        // La fermeture (isModalOpen = false) est gérée par GenericModal
    };

    // Fonction pour focus le premier champ en erreur
    const focusOnErrorField = () => {
        const errorKeys = Object.keys($form.errors);
        if (errorKeys.length > 0) {
            const firstErrorKey = errorKeys[0];
            // Recherche un input ou textarea avec le nom correspondant
            const field = document.querySelector<HTMLElement>(`[name="${firstErrorKey}"]`);
            field?.focus();
        }
    };


    // --- Logique pour la suppression et restauration (reste inchangée) ---
    function delsalon(salon) {
        const options = {
            preserveScroll: true,
            onSuccess: (data: any) => {
                const currentFlash = data.props.flash;
                 toast(currentFlash.success || 'Salon archivé.', {
                    action: {
                      label: 'Annuler',
                       // Assurez-vous que la route restore existe et fonctionne avec POST
                      onClick: () => $form.post(`/salons/${salon.id}/restore`, {
                            preserveScroll: true,
                            onSuccess: (restoreData: any) => toast.success(restoreData.props.flash.success || 'Salon restauré.'),
                            onError: () => toast.error('Erreur lors de la restauration.')
                      }),
                    },
                  });
            },
            onError: (err) => toast.error(flash?.error || 'Erreur lors de l\'archivage.')
        };
        $form.delete(`/salons/${salon.id}`, options);
    }

     function restaursalon(salon) {
        $form.post(`/salons/${salon.id}/restore`, {
            preserveScroll: true,
            onSuccess: (data: any) => {
                // toast.success(data.props.flash.success || 'Salon restauré.');
                toast(data.props.flash.success || 'Salon archivé.', {
                    action: {
                      label: 'Annuler',
                       // Assurez-vous que la route restore existe et fonctionne avec POST
                      onClick: () => $form.delete(`/salons/${salon.id}/`, {
                            preserveScroll: true,
                            onSuccess: (restoreData: any) => toast.success(restoreData.props.flash.success || 'Salon restauré.'),
                            onError: () => toast.error('Erreur lors de la restauration.')
                      }),
                    },
                  });
            
            },
            onError: () => toast.error(flash?.error || 'Erreur lors de la restauration.')
        });
    }

    function forceDeleteSalon(salon) {
         $form.delete(`/salons/${salon.id}/force-delete`, {
            preserveScroll: true,
            onSuccess: (data: any) => toast.success(data.props.flash.success || 'Salon supprimé définitivement.'),
            onError: () => toast.error(flash?.error || 'Erreur lors de la suppression définitive.')
         });
    }

</script>

<svelte:head>
    <title>Gestion des Salons</title> <!-- Titre plus spécifique -->
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="space-y-8">
        <div class="flex justify-end"> <!-- Met le bouton à droite -->
            <Button onclick={() => openModal()}>Nouveau Salon</Button> <!-- Texte spécifique -->
        </div>

        <!-- Liste des salons -->
        {#if salons.length > 0}
            {#each salons as salon (salon.id)} <!-- Ajout d'une clé pour l'itération -->
                <div class="flex items-center justify-between p-4 border rounded-lg gap-2 ">
                    <div class="flex-1 min-w-0 {salon.deleted_at ? 'opacity-50' : '' }"> <!-- Empêche le texte de déborder -->
                        <p class="font-medium truncate">{salon.name}</p>
                        <p class="text-sm text-muted-foreground truncate">{salon.description}</p>
                        {#if salon.deleted_at}
                         <p class="text-xs text-destructive">Archivé le: {new Date(salon.deleted_at).toLocaleDateString()}</p>
                        {/if}
                    </div>
                    <div class="flex gap-2 flex-shrink-0"> <!-- Empêche les boutons de passer à la ligne trop vite -->
                        <Button variant="outline" size="sm" onclick={() => openModal(salon)} disabled={!!salon.deleted_at}>
                            Éditer
                        </Button>
                        <Button
                            variant={salon.deleted_at ? 'secondary' : 'destructive'}
                            size="sm"
                            onclick={() => salon.deleted_at ? restaursalon(salon) : delsalon(salon)}
                        >
                            {salon.deleted_at ? 'Restaurer' : 'Archiver'}
                        </Button>
                        {#if salon.deleted_at}
                            <Button variant='destructive' size="sm" onclick={() => forceDeleteSalon(salon)}>
                                Suppr. Déf.
                            </Button>
                        {/if}
                    </div>
                    <!-- <pre>{JSON.stringify(salon, null, 2)}</pre> -->
                </div>
            {/each}
        {:else}
             <p class="text-center text-muted-foreground py-4">Aucun salon à afficher.</p>
        {/if}

        <!-- Utilisation du Modal Générique -->
        <GenericModal
            bind:open={isModalOpen}
            title={isEdit ? 'Éditer le Salon' : 'Nouveau Salon'}
            description="Remplissez les informations du salon ci-dessous."
            onSubmit={handleSubmit}
            isProcessing={$form.processing}
            onCancel={handleCancel}
            submitLabel={isEdit ? 'Mettre à jour' : 'Créer'}
        >
            <!-- Contenu spécifique au formulaire des Salons -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="name">Nom du salon</Label>
                    <Input id="name" name="name" bind:value={$form.name} required />
                    <InputError message={$form.errors.name} />
                </div>
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Input id="description" name="description" bind:value={$form.description} />
                    <InputError message={$form.errors.description} />
                </div>
                <div class="space-y-2 md:col-span-2">
                    <Label for="adresse">Adresse</Label>
                    <Input id="adresse" name="adresse" bind:value={$form.adresse} />
                    <InputError message={$form.errors.adresse} />
                </div>
                <div class="space-y-2">
                    <Label for="ville">Ville</Label>
                    <Input id="ville" name="ville" bind:value={$form.ville} />
                    <InputError message={$form.errors.ville} />
                </div>
                <div class="space-y-2">
                    <Label for="code_postal">Code Postal</Label>
                    <Input id="code_postal" name="code_postal" bind:value={$form.code_postal} />
                    <InputError message={$form.errors.code_postal} />
                </div>
                <div class="space-y-2">
                    <Label for="pays">Pays</Label>
                    <Input id="pays" name="pays" bind:value={$form.pays} />
                    <InputError message={$form.errors.pays} />
                </div>
            </div>
        </GenericModal>
    </div>
</AppLayout>