<script>
    // import { useForm } from '@inertiajs/inertia-svelte';
    import { inertia, useForm } from '@inertiajs/svelte';

    import {
        Button,
        buttonVariants
    } from "@/components/ui/button/index.js";
    import * as Dialog from "@/components/ui/dialog/index";
    import Label from '@/components/ui/label/label.svelte';
    import Input from '@/components/ui/input/input.svelte';
    import InputError from '@/components/InputError.svelte';

    // export let salon = null;
    // export let isOpen = false;
    // export let onClose = () => {};

    let { salon, isOpen, onClose, csrf_token } = $props()

    //   Initialisation réactive du formulaire
       const form = useForm({
        id: null,
        name: '',
        description: '',
        adresse: '',
        ville: '',
        code_postal: '',
        pays: '',
        _token: csrf_token
    });



    // // Mettez à jour le formulaire lorsque `salon` change
    $effect(() => {
        if (!salon) {
        $form.id = salon?.id ?? null;
        $form.name = salon?.name ?? '';
        $form.description = salon?.description ?? '';
        $form.adresse = salon?.pays ?? '';
        $form.ville = salon?.ville ?? '';
        $form.code_postal = salon?.code_postal ?? '';
        $form.pays = salon?.pays ?? '';
        $form._token = csrf_token ?? 'Error'
        }
    });

      // Fonction pour synchroniser les données du salon avec le formulaire
      function syncFormWithSalon() {
        if (salon) {
            $form.id = salon.id;
            $form.name = salon.name;
            $form.description = salon.description;
            $form.adresse = salon.adresse;
            $form.ville = salon.ville;
            $form.code_postal = salon.code_postal;
            $form.pays = salon.pays;
        }
    }

    // Appeler la synchronisation chaque fois que le modal est ouvert ou que `salon` change
    $effect(() => {
        syncFormWithSalon();
    });

    function submit(e) {
        e.preventDefault();
        if (salon) {
            $form.patch(`/salons/${salon.id}`,{
            preserveScroll: true,
            onSuccess: () => {
                $form.defaults() //// $form.data() contient les data
                $form.clearErrors();
                $form.reset();
                onClose();
            },
            onError: (err) => {
                console.log(err)
                console.log($form)
                // errors = err; // Affiche les erreurs retournées par le backend
            },
        });
        } else {
            $form.post('/salons/',{
            preserveScroll: true,
            onSuccess: () => {
                $form.defaults() //// $form.data() contient les data
                $form.clearErrors();
                $form.reset();
                onClose();
            },
            onError: (err) => {
                // errors = err; // Affiche les erreurs retournées par le backend
            },
        });
        }
    }
    console.log(csrf_token);
    // function submit() {
    //     console.log(salon);
    //     $form.put(`/salons/${salon.id}`,{
    //         preserveScroll: true,
    //         onSuccess: () => {
    //             $form.defaults() //// $form.data() contient les data
    //             $form.clearErrors();
    //             $form.reset();
    //             onClose();
    //         },
    //         onError: (err) => {
    //             // errors = err; // Affiche les erreurs retournées par le backend
    //         },
    //     });
    // }
</script>


<Dialog.Root open={isOpen} onOpenChange={onClose}>
    <!-- <Dialog.Trigger  class={buttonVariants({ variant: "outline" })}>
        {salon ? 'Modifier' : 'Créer'} Salon
    </Dialog.Trigger> -->
    <Dialog.Content class="sm:max-w-[425px]">
    <form class="space-y-6" onsubmit={submit}>

        <Dialog.Header>
            <Dialog.Title>{salon ? 'Modifier le Salon' : 'Créer un Salon'}</Dialog.Title>
            <Dialog.Description>
                {salon ? 'Modifiez les détails du salon.' : 'Entrez les détails du nouveau salon.'}
            </Dialog.Description>
        </Dialog.Header>
        <div class="grid gap-4 py-4">
            <div class="grid grid-cols-4 items-center gap-4">
                <Label for="name" class="text-right">Nom</Label>
                <Input id="name" bind:value={$form.name} required class="col-span-3" placeholder={$form.name} />
                {#if $form.errors.name}<span>{$form.errors.name}</span>{/if}
                <InputError message={$form.errors.name} />
            </div>
            <div class="grid grid-cols-4 items-center gap-4">
                <Label for="description" class="text-right">Description</Label>
                <Input id="description" bind:value={$form.description} class="col-span-3" />
            </div>
            <div class="grid grid-cols-4 items-center gap-4">
                <Label for="adresse" class="text-right">Adresse</Label>
                <Input id="adresse" bind:value={$form.adresse} required class="col-span-3" />
                {#if $form.errors.adresse}<span>{$form.errors.adresse}</span>{/if}
            </div>
            <div class="grid grid-cols-4 items-center gap-4">
                <Label for="ville" class="text-right">Ville</Label>
                <Input id="ville" bind:value={$form.ville} required class="col-span-3" />
                {#if $form.errors.ville}<span>{$form.errors.ville}</span>{/if}
            </div>
            <div class="grid grid-cols-4 items-center gap-4">
                <Label for="code_postal" class="text-right">Code Postal</Label>
                <Input id="code_postal" bind:value={$form.code_postal} required class="col-span-3" />
                {#if $form.errors.code_postal}<span>{$form.errors.code_postal}</span>{/if}
            </div>
            <div class="grid grid-cols-4 items-center gap-4">
                <Label for="pays" class="text-right">Pays</Label>
                <Input id="pays" bind:value={$form.pays} required class="col-span-3" />
                {#if $form.errors.pays}<span>{$form.errors.pays}</span>{/if}
            </div>
        </div>
        <Dialog.Footer>
            <Button variant="destructive" disabled={$form.processing}>
                <button type="submit">{salon ? 'Mettre à jour' : 'Créer'}</button>
            </Button>
            <!-- <Button type="button" onclick={submit}>
                {salon ? 'Mettre à jour' : 'Créer'}
            </Button> -->
            <Button type="button" onclick={onClose}>
                Fermer
            </Button>
        </Dialog.Footer>
    </form>
    </Dialog.Content>
</Dialog.Root>
<pre>{JSON.stringify($form.data(), null, 2)}</pre>

