import DataTable, {Api, Config, ConfigColumns} from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

export const defaultOptions: Config = {
    processing: true,
    serverSide: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    order: [[0, 'desc']],
    autoWidth: false,
    layout: {
        topStart: 'pageLength',
        topEnd: 'search',
        bottomStart: 'info',
        bottomEnd: 'paging',
    },
    language: {
        search: 'Buscar:',
        lengthMenu: 'Mostrar _MENU_ registros',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
        infoEmpty: 'Mostrando 0 a 0 de 0 registros',
        infoFiltered: '(filtrado de _MAX_ no total)',
        zeroRecords: 'Nenhum registro encontrado',
        paginate: {
            first: 'Primeiro',
            last: 'Último',
            next: 'Próximo',
            previous: 'Anterior',
        },
        processing: 'Carregando...',
    },
};

export class DataTableBuilder<DataType = any> {
    private readonly _el: HTMLTableElement;
    private _options: Config = { ...defaultOptions};
    private _instance: Api | null = null;
    constructor(protected element: HTMLTableElement) { this._el = element }

    public setAjax = (ajax: NonNullable<Config['ajax']>): this => {
        this._options.ajax = ajax;
        return this;
    };

    public fromDatasetUrl = (fallbackRouteNameForMsg?: string): this => {
        const url = this._el.dataset?.url;
        if (!url) {
            this.warnMissingUrl(fallbackRouteNameForMsg);
            return this;
        }
        return this.setAjax(url);
    };

    public setColumns = (columns: ConfigColumns[]): this => {
        this._options.columns = columns;
        return this;
    };

    public mergeOptions = (opts: Config): this => {
        this._options = {
            ...this._options,
            ...opts,
        };
        return this;
    };

    public setCsrf = (token?: string): this => {
        const t = token ?? (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content;
        if (!t) return this;

        const currentAjax = this._options.ajax;
        const asObj = typeof currentAjax === 'string'
            ? { url: currentAjax }
            : (currentAjax as Record<string, any>) ?? {};

        this._options.ajax = {
            type: asObj.type ?? 'POST',
            ...asObj,
            headers: {
                ...(asObj.headers ?? []),
                'X-CSFR-TOKEN': t,
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        return this;
    };

    public on = (event: string, handler: (e: Event, settings: any) => void): this => {
        const existing = (this._options as any)._deferredEvents as Array<[string, Function]> | undefined;
        (this._options as any)._deferredEvents = [...(existing ?? []), [event, handler]];
        return this;
    }

    public build = (): Api => {
        if (this._instance) {
            this.destroy();
        }

        const api = new DataTable(this._el, this._options);

        const deferred = (this._options as any)._deferredEvents as Array<[string, Function]> | undefined;
        if (deferred?.length) {
            for (const [evt, handler] of deferred) {
                api.on(evt as any, handler as any);
            }
        }

        this._instance = api;
        return api;
    };

    public destroy = () => {
        if (this._instance) {
            this._instance.destroy();
            this._instance = null;
        }
    }

    public reinit = (): Api => {
        this.destroy();
        return this.build();
    }

    public warnMissingUrl(routeNameForMsg?: string) {
        if (routeNameForMsg) {
            console.error(
                `Datatable without data-url attribute. Define data-url="{{ route('${routeNameForMsg}') }}" in the table element.`
            );
        } else {
            console.error('Datatable without data-url attribute. Define data-url="<SUA_URL>" in the table element.');
        }
    }
}
