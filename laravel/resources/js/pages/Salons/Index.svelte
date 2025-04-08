<!-- <script>
    import { Link, page } from '@inertiajs/svelte';
    // import { Button } from '@/components/ui/button';
    // import {
    //     Dialog,
    //     DialogClose,
    //     DialogContent,
    //     DialogDescription,
    //     DialogFooter,
    //     DialogHeader,
    //     DialogTitle,
    //     DialogTrigger,
    // } from '@/components/ui/dialog';
    import Label from '@/components/ui/label/label.svelte';
    import Input from '@/components/ui/input/input.svelte';
    import InputError from '@/components/InputError.svelte';
    import { inertia, useForm } from '@inertiajs/svelte';

    import {
        Button,
        buttonVariants
    } from "@/components/ui/button/index.js";
    import * as Dialog from "@/components/ui/dialog/index";



    let { auth:user, salons } = $props()

    // export let salons = [];
    let selectedSalon = null;
    let showModal = $state(false);

    function openModal(salon) {
        selectedSalon = salon;
        showModal = true;
    }

    function closeModal() {
        showModal = false;
        selectedSalon = null;
    }
</script> -->

<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    // import { Button } from '@/components/ui/button';
    // import {
    //     Dialog,
    //     DialogClose,
    //     DialogContent,
    //     DialogDescription,
    //     DialogFooter,
    //     DialogHeader,
    //     DialogTitle,
    //     DialogTrigger,
    // } from '@/components/ui/dialog';
    import Label from '@/components/ui/label/label.svelte';
    import Input from '@/components/ui/input/input.svelte';
    import InputError from '@/components/InputError.svelte';
    import { inertia, useForm } from '@inertiajs/svelte';
    import SalonDialog from './SalonDialog.svelte'; // Assurez-vous que le chemin est correct


    import {
        Button,
        buttonVariants
    } from "@/components/ui/button/index.js";
    import * as Dialog from "@/components/ui/dialog/index";



    // export let salon = null;
    // export let isOpen = false;
    // export let onClose = () => {};

    // const form = useForm({
    //     id: salon ? salon.id : null,
    //     name: salon ? salon.name : '',
    //     description: salon ? salon.description : '',
    //     adresse: salon ? salon.adresse : '',
    //     ville: salon ? salon.ville : '',
    //     code_postal: salon ? salon.code_postal : '',
    //     pays: salon ? salon.pays : '',
    // });

    // function submit() {
    //     if (salon) {
    //         form.put(`/salons/${salon.id}`);
    //     } else {
    //         form.post('/salons');
    //     }
    // }
    let { auth:user, salons, csrf_token } = $props()

    // export let salons = [];
    let selectedSalon = $state(null);
    let isDialogOpen = $state(false);

    function openDialog(salon) {
        // console.log("dsas")
        selectedSalon = salon;
        isDialogOpen = true;
    }

    function closeDialog() {
        isDialogOpen = false;
        selectedSalon = null; // Réinitialiser le salon sélectionné
    }
</script>

<h1>Liste des Salons</h1>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {#each salons as salon}
            <tr>
                <td>{salon.name}</td>
                <td>{salon.description}</td>
                <td>
                    <Button class={buttonVariants({ variant: "outline" })} onclick={() => openDialog(salon)}>
                        Modifier
                    </Button>
                </td>
            </tr>
        {/each}
        <Button class={buttonVariants({ variant: "outline" })} onclick={() => openDialog(null)}>
            Créer Salon
        </Button>
    </tbody>
</table>

<SalonDialog salon={selectedSalon} {csrf_token} isOpen={isDialogOpen} onClose={closeDialog} />

main
<pre>{JSON.stringify(selectedSalon, null, 2)}</pre>