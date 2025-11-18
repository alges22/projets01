<script setup lang="ts">
    const props = defineProps({
        data_plates: {
            type: Object,
            required: true,
        },
        plate_shapes: {
            type: Array,
            required: true,
        },
        plate_colors: {
            type: Array,
            required: true,
        },
    })

    const getShapeLabel = (id: string) => {
        const shape = props.plate_shapes.find(shape => shape.id === id);
        return shape ? shape.name : '';
    };

    const getColorLabel = (id: string) => {
        const color = props.plate_colors.find(shape => shape.id === id);
        return color ? color.label : '';
    };
</script>

<template>
    <table class="table-black">
        <tr>
            <td>Format de plaque avant</td>
            <th> {{ getShapeLabel(data_plates.front_plate_shape_id) }} </th>
        </tr>
        <tr>
            <td>Type de numéro d'immatriculation</td>
            <th>Normale</th>
        </tr>
    </table>

    <table class="table-black">
        <tr>
            <td>Forme plaque arrière</td>
            <th>{{ getShapeLabel(data_plates.back_plate_shape_id) }}</th>
        </tr>
        <tr>
            <td>Couleur de la plaque</td>
            <th>{{ getColorLabel(data_plates.plate_color_id) }}</th>
        </tr>
    </table>
</template>

<style scoped lang="scss">

</style>