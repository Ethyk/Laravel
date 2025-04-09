<!-- src/components/Modal.svelte -->

<script lang="ts">
    import { modalStore } from '@/stores/modal';
    import { onMount } from 'svelte';
    // import { goto } from '$app/navigation';
    import {
        Button,
        buttonVariants
    } from "@/components/ui/button/index.js";
        
    export let salon = null;
    export let onClose = () => {};
    
    let isSubmitting = false;
    
    async function handleSubmit() {
        if (!salon?.id) return;
        
        isSubmitting = true;
        try {
            await fetch(`/api/salons/${salon.id}`, {
                method: 'PUT',
                body: JSON.stringify(salon),
                headers: { 'Content-Type': 'application/json' }
            });
            
            modalStore.close();
            // goto('/salons');
        } finally {
            isSubmitting = false;
        }
    }
    
    $: if ($modalStore.id === null) {
        salon = null;
    }
</script>

<div class="modal-backdrop" on:click={onClose}>
    <div class="modal-content" on:click={(e) => e.stopPropagation()}>
        <form on:submit|preventDefault={handleSubmit}>
            <div class="space-y-4">
                <!-- bind:value={salon?.nom} -->
                <input
                    placeholder="Nom du salon"
                    class="w-full p-2 border rounded"
                />
                
                <!-- bind:value={salon?.description} -->
                <textarea
                    placeholder="Description"
                    class="w-full p-2 border rounded min-h-[100px]"
                />
                
                <div class="flex justify-end space-x-2">
                    <Button variant="outline" type="button" on:click={onClose}>
                        Annuler
                    </Button>
                    <Button type="submit" loading={isSubmitting}>
                        Enregistrer
                    </Button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
    }
</style>