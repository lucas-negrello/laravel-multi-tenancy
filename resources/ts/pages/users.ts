import { DataTableBuilder } from "../modules/datatables";
import {User} from "../types/models/users.model";

document.addEventListener("DOMContentLoaded", () => {
    const table = document.getElementById("users-table") as HTMLTableElement | null;
    if (!table) return;

    new DataTableBuilder<User>(table)
        .fromDatasetUrl('users.data')
        .setColumns([
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'created_at', name: 'created_at' },
        ])
        .build();
});
