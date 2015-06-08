var tmedia_session_jsep01$prev = tmedia_session_jsep01;
tmedia_session_jsep01 = function tmedia_session_jsep01(o_mgr) {
        tmedia_session_jsep.call(this, o_mgr);
        this.o_media_constraints =
        { 'mandatory':
        {
                'OfferToReceiveAudio': !!(this.e_type.i_id & tmedia_type_e.AUDIO.i_id),
                'OfferToReceiveVideo': !!(this.e_type.i_id & tmedia_type_e.VIDEO.i_id)
        },
                'optional': [{
                        DtlsSrtpKeyAgreement: false
                }]
        };

        if(tsk_utils_get_navigator_friendly_name() == 'firefox'){
                tmedia_session_jsep01.mozThis = this;
                this.o_media_constraints.mandatory.MozDontOfferDataChannel = true;
        }
};

for(var key in tmedia_session_jsep01$prev){
        if(tmedia_session_jsep01$prev.hasOwnProperty(key)){
                tmedia_session_jsep01[key] = tmedia_session_jsep01$prev[key];
        }
}
tmedia_session_jsep01.prototype = tmedia_session_jsep01$prev.prototype;
