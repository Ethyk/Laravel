<!-- src/components/GenericModal.svelte -->
<script lang="ts">
    import { Button } from "@/components/ui/button/index.js";
    import * as Dialog from "@/components/ui/dialog/index";

    let {
        children, // Le slot pour le contenu (les champs du formulaire)
        open = $bindable(false), // Contrôle l'ouverture/fermeture
        title = '', // Titre du modal
        description = '', // Description du modal
        onSubmit, // Fonction à appeler lors de la soumission du formulaire
        isProcessing = false, // Pour désactiver le bouton pendant la soumission
        onCancel = null, // Fonction optionnelle à appeler lors de l'annulation
        submitLabel = 'Enregistrer', // Texte du bouton de soumission
        cancelLabel = 'Annuler', // Texte du bouton d'annulation
        maxWidth = 'max-w-2xl' // Permet de configurer la largeur max
    } = $props();

    function handleCancel() {
        if (onCancel) {
            onCancel(); // Exécute la logique d'annulation personnalisée si fournie
        }
        open = false; // Ferme toujours le modal
    }

    // Gère la fermeture via clic extérieur ou touche Echap
    function handleOpenChange(newOpenState: boolean) {
        if (!newOpenState) {
            handleCancel();
        } else {
            open = true;
        }
    }

    function handleSubmit(e: Event) {
        e.preventDefault(); // Empêche le rechargement de la page
        if (onSubmit) {
            onSubmit(); // Appelle la fonction de soumission fournie par le parent
        }
    }

</script>

<Dialog.Root open={open} onOpenChange={handleOpenChange}>
    <!-- <Dialog.Overlay /> -->
    <Dialog.Content class={maxWidth}>
        <Dialog.Header>
            {#if title}
                <Dialog.Title>{title}</Dialog.Title>
            {/if}
            {#if description}
                <Dialog.Description>{description}</Dialog.Description>
            {/if}
            <Dialog.Close onclick={handleCancel} />
        </Dialog.Header>

        <form class="space-y-6" onsubmit={handleSubmit}>
            <div class="grid gap-4 py-4">
                <!-- Slot pour injecter les champs du formulaire -->
                {@render children()}
            </div>

            <Dialog.Footer>
                <Button type="button" variant="outline" onclick={handleCancel}>
                    {cancelLabel}
                </Button>
                <Button type="submit" disabled={isProcessing}>
                    {isProcessing ? 'En cours...' : submitLabel}
                </Button>
            </Dialog.Footer>
        </form>
    </Dialog.Content>
</Dialog.Root>