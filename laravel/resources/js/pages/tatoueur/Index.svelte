<!-- src/Pages/Tatoueur/Index.svelte -->
<script lang="ts">
    import GenericModal from '@/components/GenericModal.svelte';
    import { useForm, page } from '@inertiajs/svelte';
    import { Input } from '@/components/ui/input/index.js';
    import { Textarea } from '@/components/ui/textarea/index.js';
    import { Label } from '@/components/ui/label/index.js';
    import { Button } from '@/components/ui/button/index.js';
    import * as Select from '@/components/ui/select/index';
    import InputError from '@/components/InputError.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { type BreadcrumbItem } from '@/types';
    import { toast } from 'svelte-sonner';
    import { Trash2, Edit, PlusCircle } from 'lucide-svelte';

    // Props
    let {
        tatoueur = null,
        salonsDisponibles = [],
        csrf_token = '',
        flash = null
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Mon Profil Tatoueur', href: '/tatoueurs' },
    ];
    let isModalOpen = $state(false);
    let isEdit = $derived(!!tatoueur?.id);

    // --- Fonctions utilitaires JSON ---
    function safeStringify(obj: any): string {
        try {
            return obj ? JSON.stringify(obj, null, 2) : ''; // Pretty print, handle null/undefined
        } catch {
            return ''; // Should not happen with simple objects
        }
    }

    function safeParse(str: string): any {
        try {
            if (!str?.trim()) return {}; // Default to empty object if string is empty
            return JSON.parse(str);
        } catch (e) {
            console.warn("Invalid JSON input:", e);
            // Return a specific structure or null to indicate parse error
            return { json_error: "Format JSON invalide" };
        }
    }

    // --- Formulaire ---
    const form = useForm({
        id: tatoueur?.id ?? null,
        bio: tatoueur?.bio ?? '',
        style: tatoueur?.style ?? [], // Assume array from $casts
        disponibilites: tatoueur?.disponibilites ?? {}, // Assume object/array from $casts
        localisation_actuelle: tatoueur?.localisation_actuelle ?? null,
        instagram: tatoueur?.instagram ?? '',
        _token: csrf_token
    });

    // --- État local pour la Textarea JSON ---
    let disponibilitesJsonString = $state(safeStringify($form.disponibilites));

    // --- Synchroniser la chaîne si l'objet change (reset, load) ---
    $effect(() => {
        const currentObjectString = safeStringify($form.disponibilites);
        // Avoid infinite loops: only update if the string representation differs
        if (disponibilitesJsonString !== currentObjectString) {
            disponibilitesJsonString = currentObjectString;
            // Clear potential manual JSON error if object is reset/valid
             if ($form.errors.disponibilites === 'Format JSON invalide.') {
                 $form.clearErrors('disponibilites');
            }
        }
    });

    // --- Handlers ---
    const openModal = () => {
        $form.reset(); // Reset form data to initial (or prop data)
        $form.clearErrors();

        // Explicitly set data if editing (reset might not pick up changes if props didn't change)
        if (tatoueur) {
             $form.id = tatoueur.id;
             $form.bio = tatoueur.bio ?? '';
             $form.style = tatoueur.style ?? [];
             $form.disponibilites = tatoueur.disponibilites ?? {}; // Reset the object
             $form.localisation_actuelle = tatoueur.localisation_actuelle ?? null;
             $form.instagram = tatoueur.instagram ?? '';
        } else {
             $form.id = null;
             $form.bio = '';
             $form.style = [];
             $form.disponibilites = {}; // Reset the object
             $form.localisation_actuelle = null;
             $form.instagram = '';
        }
         // The $effect above will automatically update disponibilitesJsonString
        $form._token = csrf_token;
        isModalOpen = true;
    };

     // Handler for Textarea input to update both string and parsed object
    function handleDisponibilitesInput(event: Event) {
        const textarea = event.target as HTMLTextAreaElement;
        const currentJsonString = textarea.value;

        // 1. Update the string state (for the textarea binding)
        disponibilitesJsonString = currentJsonString;

        // 2. Attempt to parse and update the form's object state
        const parsedObject = safeParse(currentJsonString);
        $form.disponibilites = parsedObject; // Update the form state

        // 3. Clear specific JSON error if parsing now succeeds
        if ($form.errors.disponibilites === 'Format JSON invalide.' && !parsedObject?.json_error) {
            $form.clearErrors('disponibilites');
        }
        // Optional: Set error immediately for instant feedback if parse failed
        // else if (parsedObject?.json_error) {
        //    $form.setError('disponibilites', 'Format JSON invalide.');
        //}
    }

    const handleSubmit = () => {
         // Check for JSON parse error marker before submitting
         if ($form.disponibilites?.json_error) {
             $form.setError('disponibilites', 'Format JSON invalide.'); // Set formal error
             toast.error('Veuillez corriger le format JSON des disponibilités.');
             focusOnErrorField(); // Focus on the textarea
             return; // Prevent submission
         }

         const options = {
            preserveScroll: true,
            onSuccess: (pageData: any) => {
                const currentFlash = pageData.props.flash;
                toast.success(currentFlash?.success || `Profil ${isEdit ? 'mis à jour' : 'créé'} avec succès.`);
                isModalOpen = false;
                // Le formulaire se réinitialisera automatiquement car la page recharge les props
            },
            onError: (errors: any) => {
                 toast.error(flash?.error || 'Une erreur est survenue. Veuillez vérifier les champs.');
                 focusOnErrorField();
            }
        };
        if (isEdit) {
             $form.patch(`/tatoueurs/${$form.id}`, options);
        } else {
             $form.post('/tatoueurs', options);
        }
    };

    const handleCancel = () => {
        $form.clearErrors();
        // $form.reset(); // Reset est implicite à la réouverture ou si on ferme sans sauver
        // La fermeture (isModalOpen = false) est gérée par GenericModal
    };

    const focusOnErrorField = () => {
        const errorKeys = Object.keys($form.errors);
        if (errorKeys.length > 0) {
            const firstErrorKey = errorKeys[0];
            // Ensure the textarea has name="disponibilites"
            const field = document.querySelector<HTMLElement>(`[name="${firstErrorKey}"]`);
            field?.focus();
        }
    };

    const handleDelete = () => {
        if (confirm('Êtes-vous sûr de vouloir supprimer définitivement votre profil tatoueur ?')) {
             const options = {
                 preserveScroll: true,
                 onSuccess: (pageData: any) => {
                     const currentFlash = pageData.props.flash;
                     toast.success(currentFlash?.success || 'Profil supprimé.');
                     // La page va se recharger, le composant affichera l'état "pas de profil"
                 },
                 onError: () => {
                     toast.error(flash?.error || 'Erreur lors de la suppression.');
                 }
             };
             $form.delete(`/tatoueurs/${tatoueur.id}`, options);
        }
    };
    function displayStyles(styles: any): string {
        if (Array.isArray(styles)) return styles.join(', ');
        if (typeof styles === 'object' && styles !== null) return JSON.stringify(styles); // Fallback
        return styles || 'Non défini'; // Gère null, undefined, chaîne vide
    }

    function displayDisponibilites(dispo: any): string {
         if (typeof dispo === 'object' && dispo !== null && Object.keys(dispo).length > 0) {
             return JSON.stringify(dispo, null, 2); // Afficher le JSON formaté
         }
         return dispo || 'Non défini';
    }

    // --- Composant simple pour éditer les styles (exemple basique) ---
    // Pourrait être remplacé par une librairie de "tags input"
    function handleStyleInput(e: Event) {
      const input = e.target as HTMLInputElement;
      // Convertit la chaîne séparée par des virgules en tableau
      $form.style = input.value.split(',').map(s => s.trim()).filter(s => s !== '');
    }

</script>

<svelte:head>
    <title>Mon Profil Tatoueur</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <div class="space-y-8">
        {#if tatoueur}
            <!-- Affichage du Profil Existant -->
            <div class="p-6 border rounded-lg shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-semibold">Votre Profil Tatoueur</h2>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" onclick={openModal}>
                            <Edit class="w-4 h-4 mr-2" /> Éditer
                        </Button>
                        <Button variant="destructive" size="sm" onclick={handleDelete}>
                            <Trash2 class="w-4 h-4 mr-2" /> Supprimer
                        </Button>
                    </div>
                </div>

                <div>
                    <h3 class="font-medium mb-1">Bio</h3>
                    <p class="text-muted-foreground">{tatoueur.bio || 'Non définie'}</p>
                </div>

                <div>
                    <h3 class="font-medium mb-1">Styles</h3>
                    <!-- Utiliser la fonction displayStyles -->
                    <p class="text-muted-foreground">{displayStyles(tatoueur.style)}</p>
                </div>

                 <div>
                    <h3 class="font-medium mb-1">Salon Actuel</h3>
                    <p class="text-muted-foreground">{tatoueur.salonActuel?.name || 'Non défini'}</p>
                </div>

                <div>
                    <h3 class="font-medium mb-1">Instagram</h3>
                    {#if tatoueur.instagram}
                         <a href={tatoueur.instagram} target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline break-all">{tatoueur.instagram}</a>
                    {:else}
                        <p class="text-muted-foreground">Non défini</p>
                    {/if}
                </div>

                 <div>
                    <h3 class="font-medium mb-1">Disponibilités (JSON brut)</h3>
                    <!-- Utiliser la fonction displayDisponibilites -->
                    <pre class="text-xs bg-gray-100 p-2 rounded overflow-x-auto"><code>{displayDisponibilites(tatoueur.disponibilites)}</code></pre>
                </div>

                 <!-- Afficher d'autres infos si nécessaire : user, flashs, portfolios... -->

            </div>

        {:else}
            <!-- Invitation à Créer un Profil -->
             <div class="text-center p-6 border rounded-lg shadow-sm">
                 <p class="text-muted-foreground mb-4">Vous n'avez pas encore de profil tatoueur.</p>
                <Button onclick={openModal}>
                    <PlusCircle class="w-4 h-4 mr-2" /> Créer mon profil
                </Button>
            </div>
        {/if}

        <GenericModal
            bind:open={isModalOpen}
            title={isEdit ? 'Modifier mon Profil' : 'Créer mon Profil'}
            description="Renseignez les informations de votre profil tatoueur."
            isProcessing={$form.processing}
            onSubmit={handleSubmit}
            onCancel={handleCancel}
            submitLabel={isEdit ? 'Mettre à jour' : 'Créer'}

        >
            <div class="grid grid-cols-1 gap-4">
                <div class="space-y-2">
                    <Label for="bio">Bio</Label>
                    <Textarea id="bio" name="bio" bind:value={$form.bio} placeholder="Décrivez votre parcours, votre style..." />
                    <InputError message={$form.errors.bio} />
                </div>

                <div class="space-y-2">
                    <Label for="style">Styles (séparés par des virgules)</Label>
                    <!-- Input simple pour les styles (tags) -->
                    <Input
                       id="style"
                       name="style"
                       placeholder="ex: Old School, Japonais, Dotwork"
                       value={$form.style?.join(', ') ?? ''}
                       oninput={handleStyleInput}
                    />
                    <!-- Afficher une prévisualisation ou un composant de tags ici si besoin -->
                     <InputError message={$form.errors.style} />
                     {#if typeof $form.errors.style === 'string' && $form.errors.style.toLowerCase().includes('json')}
                        <p class="text-sm text-destructive">Assurez-vous que les styles sont correctement formatés.</p>
                     {/if}
                </div>

                <div class="space-y-2">
                    <Label for="localisation_actuelle">Salon Actuel (Optionnel)</Label>
                    <Select.Root name="localisation_actuelle" bind:value={$form.localisation_actuelle}>
                        <Select.Trigger id="localisation_actuelle" placeholder="Sélectionnez un salon" />
                        <Select.Content>
                            <Select.Item value={null}>Aucun</Select.Item> {#if !$form.localisation_actuelle} (Sélectionné) {/if}
                            {#each salonsDisponibles as salon (salon.id)}
                                <Select.Item value={salon.id}>{salon.name} ({salon.ville})</Select.Item> {#if $form.localisation_actuelle === salon.id} (Sélectionné) {/if}
                            {/each}
                        </Select.Content>
                    </Select.Root>
                     <InputError message={$form.errors.localisation_actuelle} />
                </div>

                <div class="space-y-2">
                    <Label for="instagram">Lien Instagram (URL complète)</Label>
                    <Input id="instagram" name="instagram" type="url" bind:value={$form.instagram} placeholder="https://instagram.com/votreprofil" />
                    <InputError message={$form.errors.instagram} />
                </div>

                <div class="space-y-2">
                    <Label for="disponibilites">Disponibilités (JSON)</Label>
                    <Textarea
                        id="disponibilites"
                        name="disponibilites" 
                        rows={5}
                        placeholder={'{\n  "lundi": "fermé",\n  "mardi": "10h-18h",\n  ...\n}'}
                        bind:value={disponibilitesJsonString} 
                        oninput={handleDisponibilitesInput} 
                        aria-invalid={$form.errors.disponibilites ? 'true' : undefined}
                    />
                    <InputError message={$form.errors.disponibilites} />
                    {#if $form.disponibilites?.json_error && $form.errors.disponibilites !== 'Format JSON invalide.'}
                        <!-- Show instant feedback only if not already covered by backend error -->
                         <p class="text-sm text-destructive">Format JSON invalide.</p>
                     {/if}
                </div>
            </div>
        </GenericModal>
    </div>
</AppLayout>