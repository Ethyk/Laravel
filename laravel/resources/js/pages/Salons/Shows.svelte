<script>
    import { inertia, useForm, page } from '@inertiajs/svelte';

    // import { useForm } from '@inertiajs/svelte';

    // export let salon; // Réception des données du contrôleur

    // let updatedSalon = { ...salon }; // Copie pour permettre l'édition
    let errors = {}; // Gestion des erreurs
    let salon = $derived($page.props.salon|| null); 

    
    const form = useForm({
        ...salon,
        // 'salon': salon.id
        // name: salon.name,
        // description:salon.description,
        // adresse: salon.adresse,
        // ville: salon.ville,
        // code_postal: salon.code_postal,
        // pays: salon.pays,
    });

    // let { status, canResetPassword }: Props = $props();

    // const submit = () => {
    //     // e.preventDefault();
    //     $form.put(`/salons/${salon.id}`, {
    //         onFinish: () => $form.reset('password'),
    //     });
    // };
    const submitChanges = (e) => {
        e.preventDefault();
        $form.patch(route('salons.update', salon), {
            preserveScroll: true,
        });
    };
    // const submitChanges = () => {
    //     form.put(`/salons/${salon.id}`, updatedSalon, {
    //         onSuccess: () => {
    //             alert('Salon modifié avec succès !');
    //         },
    //         onError: (err) => {
    //             errors = err; // Affiche les erreurs retournées par le backend
    //         },
    //     });
    // };
</script>

<div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6">Modifier le Salon</h1>

    <form on:submit|preventDefault={submitChanges} class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium">Nom du Salon</label>
            <input
                id="name"
                type="text"
                bind:value={$form.name}
                class="w-full border rounded px-4 py-2"
                placeholder="Nom du salon"
            />
            {#if $form.errors.name}
                <p class="text-red-500 text-sm mt-1">{$form.errors.name}</p>
            {/if}
        </div>

        <div>
            <label for="description" class="block text-sm font-medium">Description</label>
            <textarea
                id="description"
                bind:value={$form.description}
                class="w-full border rounded px-4 py-2"
                placeholder="Description du salon"
            ></textarea>
            {#if $form.errors.description}
                <p class="text-red-500 text-sm mt-1">{$form.errors.description}</p>
            {/if}
        </div>

        <div>
            <label for="adresse" class="block text-sm font-medium">Adresse</label>
            <input
                id="adresse"
                type="text"
                bind:value={$form.adresse}
                class="w-full border rounded px-4 py-2"
                placeholder="Adresse"
            />
            {#if $form.errors.adresse}
                <p class="text-red-500 text-sm mt-1">{$form.errors.adresse}</p>
            {/if}
        </div>

        <div>
            <label for="ville" class="block text-sm font-medium">Ville</label>
            <input
                id="ville"
                type="text"
                bind:value={$form.ville}
                class="w-full border rounded px-4 py-2"
                placeholder="Ville"
            />
            {#if $form.errors.ville}
                <p class="text-red-500 text-sm mt-1">{form.errors.ville}</p>
            {/if}
        </div>

        <div>
            <label for="code_postal" class="block text-sm font-medium">Code Postal</label>
            <input
                id="code_postal"
                type="text"
                bind:value={$form.code_postal}
                class="w-full border rounded px-4 py-2"
                placeholder="Code Postal"
            />
            {#if $form.errors.code_postal}
                <p class="text-red-500 text-sm mt-1">{form.errors.code_postal}</p>
            {/if}
        </div>

        <div>
            <label for="pays" class="block text-sm font-medium">Pays</label>
            <input
                id="pays"
                type="text"
                bind:value={$form.pays}
                class="w-full border rounded px-4 py-2"
                placeholder="Pays"
            />
            {#if $form.errors.pays}
                <p class="text-red-500 text-sm mt-1">{form.errors.pays}</p>
            {/if}
        </div>

        <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
            Modifier le Salon
        </button>
    </form>

    <div class="mt-6">
        <a href="/salons" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Retour à la liste des salons
        </a>
    </div>
</div>
