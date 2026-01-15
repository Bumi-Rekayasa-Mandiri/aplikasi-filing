const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"filing.surat.index":{"uri":"filing\/surat","methods":["GET","HEAD"]},"filing.surat.show":{"uri":"filing\/surat\/{surat}","methods":["GET","HEAD"],"parameters":["surat"],"bindings":{"surat":"id"}},"filing.surat.update":{"uri":"filing\/surat\/{surat}","methods":["PUT"],"parameters":["surat"],"bindings":{"surat":"id"}},"filing.surat.upload":{"uri":"filing\/surat\/{surat}\/upload","methods":["POST"],"parameters":["surat"],"bindings":{"surat":"id"}},"filing.filing.surat.upload-pdf":{"uri":"filing\/filing\/surat\/{surat}\/upload-pdf","methods":["POST"],"parameters":["surat"],"bindings":{"surat":"id"}},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
