!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.CKEditor5=t():(e.CKEditor5=e.CKEditor5||{},e.CKEditor5.uswds_accordion=t())}(self,(()=>(()=>{var e={"ckeditor5/src/core.js":(e,t,o)=>{e.exports=o("dll-reference CKEditor5.dll")("./src/core.js")},"ckeditor5/src/ui.js":(e,t,o)=>{e.exports=o("dll-reference CKEditor5.dll")("./src/ui.js")},"ckeditor5/src/widget.js":(e,t,o)=>{e.exports=o("dll-reference CKEditor5.dll")("./src/widget.js")},"dll-reference CKEditor5.dll":e=>{"use strict";e.exports=CKEditor5.dll}},t={};function o(n){var i=t[n];if(void 0!==i)return i.exports;var r=t[n]={exports:{}};return e[n](r,r.exports,o),r.exports}o.d=(e,t)=>{for(var n in t)o.o(t,n)&&!o.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t);var n={};return(()=>{"use strict";o.d(n,{default:()=>h});var e=o("ckeditor5/src/core.js"),t=o("ckeditor5/src/widget.js");class i extends e.Command{execute(){const{model:e}=this.editor;e.change((t=>{e.insertContent(function(e){const t=e.createElement("accordion"),o=e.createElement("accordionTitle"),n=e.createElement("accordionContent");return e.append(o,t),e.append(n,t),e.appendElement("paragraph",n),t}(t))}))}refresh(){const{model:e}=this.editor,{selection:t}=e.document,o=e.schema.findAllowedParent(t.getFirstPosition(),"accordion");this.isEnabled=null!==o}}class r extends e.Command{constructor(e,t={}){super(e),this.order=t.order||"below"}execute(){const e=this.editor,t=e.model.document.selection;let o=null;t.getFirstPosition().getAncestors().forEach((e=>{"accordionContent"!==e.name&&"accordionTitle"!==e.name||(o=e)})),null!=o&&e.model.change((e=>{let t;if("below"===this.order){let n="accordionContent"===o.name?o.index:o.index+1;n<0&&(n=0),t=e.createPositionAfter(o.parent.getChild(n))}else{let n="accordionContent"===o.name?o.index-1:o.index;n<0&&(n=0),t=e.createPositionBefore(o.parent.getChild(n))}const n=e.createElement("accordionTitle"),i=e.createElement("accordionContent");e.insertText("Accordion title",n),e.insert(i,t),e.insert(n,t);const r=e.createElement("paragraph");e.appendText("Accordion content.",r),e.insert(r,i)}))}refresh(){this.isEnabled=!0}}class s extends e.Command{constructor(e,t={}){super(e)}execute(){const e=this.editor,t=e.model.document.selection;let o=null;t.getFirstPosition().getAncestors().forEach((e=>{"accordionContent"!==e.name&&"accordionTitle"!==e.name||(o=e)})),null!=o&&e.model.change((e=>{let t,n;n="accordionContent"===o.name?o.index-1:o.index+1,t=o.parent.getChild(n),e.remove(o),e.remove(t)}))}refresh(){this.isEnabled=!0}}class c extends e.Plugin{static get pluginName(){return"AccordionEditing"}static get requires(){return[t.Widget]}init(){this._defineSchema(),this._defineConverters(),this.editor.commands.add("insertAccordion",new i(this.editor)),this.editor.commands.add("insertAccordionRowAbove",new r(this.editor,{order:"above"})),this.editor.commands.add("insertAccordionRowBelow",new r(this.editor,{order:"below"})),this.editor.commands.add("deleteAccordionRow",new s(this.editor,{}))}_defineSchema(){const e=this.editor.model.schema;e.register("accordion",{isObject:!0,allowWhere:"$block"}),e.register("accordionRow",{allowIn:"accordion",isLimit:!0}),e.register("accordionTitle",{isLimit:!0,allowIn:"accordion",allowContentOf:"$block"}),e.register("accordionContent",{isLimit:!0,allowIn:"accordion",allowContentOf:"$root"}),e.addChildCheck(((e,t)=>{if((e.endsWith("accordionContent")||e.endsWith("accordionTitle"))&&"accordion"===t.name)return!1}))}_defineConverters(){const{conversion:e}=this.editor,o=this.editor.config.get("uswdsAccordionConfig").options;let n=!1,i="usa-accordion";o&&(n=o.bordered,n&&(i+=" usa-accordion--bordered")),e.for("upcast").elementToElement({model:"accordion",view:{name:"div",classes:"usa-accordion"}}),e.for("upcast").add((e=>{e.on("element:h4",((e,t,o)=>{const{consumable:n,writer:i,safeInsert:r,convertChildren:s,updateConversionResult:c}=o,{viewItem:d}=t,a={name:!0,classes:"usa-accordion__heading"},l={name:!0,classes:"usa-accordion__button"};if(!n.test(d,a))return;if(1!==d.childCount)return;const u=d.getChild(0);if(!u.is("element","div"))return;if(!n.test(u,l))return;const m=i.createElement("accordionTitle");r(m,t.modelCursor)&&(n.consume(d,a),n.consume(u,l),s(u,m),c(m,t))}))})),e.for("upcast").elementToElement({model:"accordionContent",view:{name:"div",classes:"usa-accordion__content"}}),e.for("dataDowncast").elementToElement({model:"accordion",view:{name:"div",classes:i}}),e.for("dataDowncast").add((e=>{e.on("insert:accordionTitle",((e,t,o)=>{if(!o.consumable.consume(t.item,"insert"))return;const{writer:n,mapper:i}=o,r=i.toViewPosition(t.range.start),s=n.createContainerElement("h4",{class:"usa-accordion__heading"}),c=n.createEditableElement("div",{class:"usa-accordion__button",type:"button","aria-expanded":!1});n.insert(n.createPositionAt(s,0),c),i.bindElements(t.item,c),n.insert(r,s)}))})),e.for("dataDowncast").elementToElement({model:"accordionContent",view:(e,{writer:t})=>t.createContainerElement("div",{class:"usa-accordion__content usa-prose"})}),e.for("editingDowncast").elementToElement({model:"accordion",view:(e,{writer:o})=>{const n=o.createContainerElement("div",{class:i});return(0,t.toWidget)(n,o)}}),e.for("editingDowncast").elementToElement({model:"accordionTitle",view:(e,{writer:o})=>{const n=o.createEditableElement("div",{class:"ckeditor-accordion-title"});return(0,t.toWidgetEditable)(n,o)}}),e.for("editingDowncast").elementToElement({model:"accordionContent",view:(e,{writer:o})=>{const n=o.createEditableElement("div",{class:"ckeditor-accordion-content"});return(0,t.toWidgetEditable)(n,o)}})}}var d=o("ckeditor5/src/ui.js");class a extends e.Plugin{static get requires(){return[d.ContextualBalloon]}init(){const e=this.editor,t=this.editor.t;e.ui.componentFactory.add("UswdsAccordionToolbar",(o=>{const n=e.commands.get("insertAccordion"),i=new d.ButtonView(o);return i.set({label:t("USWDS Accordion"),icon:'<?xml version="1.0" encoding="UTF-8"?>\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n<svg width="17px" height="17px" viewBox="0 0 17 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">\n<g>\n</g>\n\t<path d="M0 0v17h17v-17h-17zM16 1v9h-15v-9h15zM16 11v2h-15v-2h15zM1 16v-2h15v2h-15z" fill="#000000"/>\n</svg>',tooltip:!0}),i.bind("isOn","isEnabled").to(n,"value","isEnabled"),this.listenTo(i,"execute",(()=>e.execute("insertAccordion"))),i})),e.ui.componentFactory.add("accordionAddAbove",(o=>{const n=e.commands.get("insertAccordionRowAbove"),i=new d.ButtonView(o);return i.set({label:t("Insert row above"),iconAddAbove:'<?xml version="1.0" encoding="utf-8"?>\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n<svg width="76px" height="76px" viewBox="0 0 76 76" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" baseProfile="full" enable-background="new 0 0 76.00 76.00" xml:space="preserve">\n\t<path fill="#000000" fill-opacity="1" stroke-width="0.2" stroke-linejoin="round" d="M 14,27L 46,27L 46,38L 40,38L 40,54L 14,54L 14,27 Z M 43,35L 43,30L 17,30L 17,35L 43,35 Z M 37,38L 17,38L 17,43L 37,43L 37,38 Z M 37,46L 17,46L 17,51L 37,51L 37,46 Z M 50,30L 55,30L 55,25L 60,25L 60,30L 65,30L 65,35L 60,35L 60,40L 55,40L 55,35L 50,35L 50,30 Z "/>\n</svg>\n',tooltip:!1,withText:!0}),i.bind("isOn","isEnabled").to(n,"value","isEnabled"),this.listenTo(i,"execute",(()=>e.execute("insertAccordionRowAbove"))),i})),e.ui.componentFactory.add("accordionAddBelow",(o=>{const n=e.commands.get("insertAccordionRowBelow"),i=new d.ButtonView(o);return i.set({label:t("Insert row below"),iconAddBelow:'<?xml version="1.0" encoding="utf-8"?>\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n<svg width="76px" height="76px" viewBox="0 0 76 76" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" baseProfile="full" enable-background="new 0 0 76.00 76.00" xml:space="preserve">\n\t<path fill="#000000" fill-opacity="1" stroke-width="0.2" stroke-linejoin="round" d="M 50,48L 50,43L 55,43L 55,38L 60,38L 60,43L 65,43L 65,48L 60,48L 60,53L 55,53L 55,48L 50,48 Z M 14,51L 14,24L 40,24L 40,40L 46,40L 46,51L 14,51 Z M 43,43L 17,43L 17,48L 43,48L 43,43 Z M 37,40L 37,35L 17,35L 17,40L 37,40 Z M 37,32L 37,27L 17,27L 17,32L 37,32 Z "/>\n</svg>\n',tooltip:!1,withText:!0}),i.bind("isOn","isEnabled").to(n,"value","isEnabled"),this.listenTo(i,"execute",(()=>e.execute("insertAccordionRowBelow"))),i})),e.ui.componentFactory.add("accordionRemove",(o=>{const n=e.commands.get("deleteAccordionRow"),i=new d.ButtonView(o);return i.set({label:t("Delete row"),iconDelete:'<?xml version="1.0" encoding="utf-8"?>\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n    <path d="M5 3H3v18h18V3H5zm14 2v14H5V5h14zm-3 6H8v2h8v-2z" fill="currentColor"/>\n</svg>\n',tooltip:!1,withText:!0}),i.bind("isOn","isEnabled").to(n,"value","isEnabled"),this.listenTo(i,"execute",(()=>e.execute("deleteAccordionRow"))),i}))}}function l(e){const t=e.getSelectedElement();return t&&m(t)?t:null}function u(e){const t=e.getFirstPosition();if(!t)return null;let o=t.parent;for(;o;){if(o.is("element")&&m(o))return o;o=o.parent}return null}function m(e){return!!e.hasClass("usa-accordion")&&(0,t.isWidget)(e)}class w extends e.Plugin{static get requires(){return[t.WidgetToolbarRepository]}static get pluginName(){return"AccordionToolbar"}afterInit(){const e=this.editor,o=e.t,n=e.plugins.get(t.WidgetToolbarRepository),i=e.config.get("uswdsAccordionConfig.contentToolbar"),r=e.config.get("uswdsAccordionConfig.tableToolbar");i&&n.register("accordionContent",{ariaLabel:o("USWDS Accordion toolbar"),items:i,getRelatedElement:u}),r&&n.register("accordion",{ariaLabel:o("USWDS Accordion toolbar"),items:r,getRelatedElement:l})}}class g extends e.Plugin{static get requires(){return[c,a,w]}}const h={UswdsAccordion:g}})(),n=n.default})()));