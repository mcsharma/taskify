export default class EnumBase {

    private static all: any[];
    public static getAll<T>(): T[] {
        if (this.all === void 0) {
            this.all = [];
            let self = <any>this;
            for (var key in self) {
                if (!self.hasOwnProperty(key)) {
                    continue;
                }
                if (typeof self[key] === 'string' || typeof self[key] === 'number') {
                    this.all.push(self[key]);
                }
            }
        }
        return this.all as T[];
    }
}