<script>
    import { inertia, useForm } from '@inertiajs/svelte';

    const form = useForm({
        name: '',
        description: '',
        adresse: '',
        ville: '',
        code_postal: '',
        pays: '',
    });

    function submit() {
        form.post('/salons');
    }
</script>

<h1>Créer un Salon</h1>
<form on:submit|preventDefault={submit}>
    <div>
        <label>Nom:</label>
        <input type="text" bind:value={$form.name} required />
        {#if $form.errors.name}<span>{$form.errors.name}</span>{/if}
    </div>
    <div>
        <label>Description:</label>
        <textarea bind:value={f$orm.description}></textarea>
    </div>
    <div>
        <label>Adresse:</label>
        <input type="text" bind:value={form.adresse} required />
        {#if form.errors.adresse}<span>{form.errors.adresse}</span>{/if}
    </div>
    <div>
        <label>Ville:</label>
        <input type="text" bind:value={form.ville} required />
        {#if form.errors.ville}<span>{form.errors.ville}</span>{/if}
    </div>
    <div>
        <label>Code Postal:</label>
        <input type="text" bind:value={form.code_postal} required />
        {#if form.errors.code_postal}<span>{form.errors.code_postal}</span>{/if}
    </div>
    <div>
        <label>Pays:</label>
        <input type="text" bind:value={form.pays} required />
        {#if form.errors.pays}<span>{form.errors.pays}</span>{/if}
    </div>
    <button type="submit">Créer</button>
</form>
