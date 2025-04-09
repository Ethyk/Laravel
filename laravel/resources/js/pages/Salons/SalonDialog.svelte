<script>
    import { inertia, useForm } from '@inertiajs/svelte';
    import {
        Button,
        buttonVariants
    } from "@/components/ui/button/index.js";
    import * as Dialog from "@/components/ui/dialog/index";
    import Label from '@/components/ui/label/label.svelte';
    import Input from '@/components/ui/input/input.svelte';
    import InputError from '@/components/InputError.svelte';
  
    let { salon = null, isOpen = false, onClose = () => {}, csrf_token = 0x42 } = $props();

     const defaultForm = $state({
        id: null,
        name: '',
        description: '',
        adresse: '',
        ville: '',
        code_postal: '',
        pays: '',
        _token: '' //csrf_token
    });
    const form = useForm(defaultForm);
    let prevId = $state(null);
    //TODO fix create salon
    $effect(() => {
        if (salon?.id === prevId) 
            return ;
        $form.defaults({...defaultForm, ...(salon || {}), _token: csrf_token}).reset()
        prevId = salon?.id
        
    });

    function submit(e) {
        e.preventDefault();
        const options = {
            preserveScroll: true,
            onSuccess: () => {
                $form.defaults({...defaultForm}).clearErrors().reset();
                onClose();
            },
            onError: (err) => console.log(err)
        };
        salon
            ? $form.patch(`/salons/${salon.id}`, options)
            : $form.post(`/salons/`, options);
    }
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
                <!-- {#if $form.errors.name}<span>{$form.errors.name}</span>{/if} -->
                <InputError  message={$form.errors.name} />
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

Debug [form.data]:
<!-- <pre>{JSON.stringify($form.isDirty, null, 2)}</pre> -->
<pre>{JSON.stringify($form.data(), null, 2)}</pre>
