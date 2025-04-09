<!-- src/routes/salons/index.svelte -->

<script lang="ts">
    import { onMount } from 'svelte';
    import { modalStore } from '@/stores/modal';
    import { Button } from '@/components/ui/button';
    import * as Table from '@/components/ui/table';
    import Modal from './newModal.svelte';

    // import Button from '@/components/ui/button';

    // export let salons = [];
    let { auth:user, salons, csrf_token } = $props()


    onMount(async () => {
        // Initialisation des données si nécessaire
        // if (!salons.length) {
        //     await refreshSalons();
        // }
    });

    async function refreshSalons() {
        const response = await fetch('/api/salons');
        const data = await response.json();
        salons = data;
    }

    function editSalon(id: number) {
        // modalStore.open('edit-salon', id);
        modalStore.update(n => ({
            id: 'edit-salon',
            params: { salonId: id }
        }));
        console.log($modalStore.params.salonId)
    }
</script>

<main class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Mes Salons</h1>
        <Button onclick={refreshSalons}>Rafraîchir</Button>
    </div>

    <Table.Root>
        <Table.Header>
            <Table.Row>
                <Table.Head>Nom</Table.Head>
                <Table.Head>Description</Table.Head>
                <Table.Head>Actions</Table.Head>
            </Table.Row>
        </Table.Header>
        <Table.Body>
            {#each salons as salon}
                <Table.Row>
                    <Table.Cell>{salon.nom}</Table.Cell>
                    <Table.Cell>{salon.description}</Table.Cell>
                    <Table.Cell>
                        <Button variant="outline" size="sm" onclick={() => editSalon(salon.id)}>
                            Modifier
                        </Button>
                    </Table.Cell>
                </Table.Row>
            {/each}
        </Table.Body>
    </Table.Root>
</main>
<Modal salon={$modalStore.params.salonId} {csrf_token} isOpen={false} onClose={() => {}} />
