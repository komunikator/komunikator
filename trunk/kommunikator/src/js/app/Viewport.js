1	Ext.define('app.Viewport', {
2	    extend  : 'Ext.container.Viewport',
3	   
4	    style   : 'padding : 2px 10px',  // отступы: верх, низ - 2; право, лево - 10
5	    layout  : 'border',
6	   
7	    items   : [{
8	        region   : 'north',  // верх
9	        // autoHeight : true,
10	        border   : false,
11	        margins  : '0 0 5 0'
12	    }, {
13	        region   : 'south',  // низ
14	        title    : '<div style="text-align : center"><p style="font-size : 8pt">'+app.msg.copyright+'</p></div>',  // Телефонные системы®PBX © 2012
15	        border   : false,
16	        margins  : '10 0 10 0'
17	    }, {
18	        region       : 'west',  // лево
19	        collapsible  : true,
20	        title        : app.msg.pbx_status,  // Статус АТС
21	        // autoHeight : true,
22	        // height : 100,
23	        width        : 270,
24	        // collapsed : true,
25	       
26	        listeners    : {
27	            afterrender : function() {
28	                this.fireEvent('expand', this);
29	            },
30	                   
31	            expand      : function(i) {
32	                i.items.each(function(s) {
33	                    if (s && s.store) {
34	                        app.set_autorefresh(s, true);
35	                    // console.log('owner expand for: '+s.store.storeId);
36	                    }
37	                })
38	            },
39	                   
40	            collapse    : function(i) {
41	                i.items.each(function(s) {
42	                    if (s && s.store) {
43	                        app.set_autorefresh(s, false);
44	                    // console.log('owner collapse for: '+s.store.storeId);
45	                    }       
46	                })
47	            }
48	        },
49	
50	        // split : true,
51	
52	        items        : [
53	            Ext.create('app.module.Status_Grid', {
54	                title : app.msg.statistic  // Статистика АТС
55	            })
56	        ]
57	   
58	    }, {
59	        region     : 'center',  // центр
60	        // resizable : true,
61	        // split : true,
62	        // layout : 'anchor',
63	        layout     : 'fit',
64	        // autoHeight : true,
65	        xtype      : 'tabpanel',
66	        // layout : 'accordion',
67	        id         : 'main_tabpanel',
68	        bodyStyle  : 'padding : 15px',  // отступы: верх, низ, право, лево - 15
69	        // style : 'padding : 2px',
70	        // defaults : {
71	        //     layout : 'fit'
72	        // },   
73	        // activeTab : 0,
74	       
75	        listeners  : {
76	           
77	            afterrender : function() {
78	                var f = this.setActiveTab(0);
79	                // if (f && f.items) ;
80	                // grid.ownerCt.layout.setActiveItem(grid.ownerCt.items.indexOf(grid));
81	                // console.log(
82	                f.items.items[0].fireEvent('activate', f.items.items[0]);  // f.setActiveItem(0);
83	            },
84	                   
85	            tabchange   : function(c, f, o) {
86	                if (f && f.items) {
87	                    f.getLayout().setActiveItem(0);
88	                    f.items.items[0].fireEvent('activate', f.items.items[0]);  // f.setActiveItem(0);
89	                }
90	            }
91	
92	        },
93	               
94	        items      : [
95	   
96	        // Ext.create('app.module.Attendant_Panel'),
97	        // Ext.create('app.module.Extensions_Panel'),
98	       
99	        Ext.create('app.Card_Panel', {
100	            title: app.msg.directory,  // Справочники
101	            items: [
102	            Ext.create('app.module.Extensions_Grid', {
103	                title: app.msg.extensions  // Внутренние номера
104	            }),
105	            Ext.create('app.module.Groups_Grid', {
106	                title: app.msg.groups  // Группы
107	            }),
108	            Ext.create('app.module.AddressBook_Grid', {
109	                title: app.msg.address_book  // Адресная книга
110	            })
111	            ]
112	        }),
113	       
114	        Ext.create('app.Card_Panel', {
115	            title: app.msg.attendant,  // Автосекретарь
116	            items: [
117	            Ext.create('app.module.Prompts_Panel', {
118	                // title: '<center>'+app.msg.prompts+'</center>'
119	                title: app.msg.prompts  // Приветствия
120	            }),
121	            Ext.create('app.module.Keys_Grid', {
122	                title: app.msg.keys  // Меню приветствия
123	            }),
124	            Ext.create('app.module.Time_Frames_Grid', {
125	                title: app.msg.timeframes  // Расписание рабочего времени
126	            })
127	            ]
128	        }),
129	       
130	        Ext.create('app.Card_Panel', {
131	            title: app.msg.routing,  // Маршрутизация
132	            items: [
133	            Ext.create('app.module.DID_Grid', {
134	                title: app.msg.routing_rules  // Правила маршрутизации
135	            }),
136	            Ext.create('app.module.Dial_plans_Grid', {
137	                title: app.msg.dial_plans  // Правила набора номера
138	            }),
139	            Ext.create('app.module.Conferences_Grid', {
140	                title: app.msg.conferences  // Конференции
141	            }),
142	            Ext.create('app.module.Gateways_Grid', {
143	                title: app.msg.gateways  // Провайдеры
144	            })
145	            ]
146	        }),
147	       
148	        Ext.create('app.Card_Panel', {
149	            title: app.msg.music_on_hold,  // Музыка на удержании
150	            items: [         
151	            Ext.create('app.module.Music_On_Hold_Grid', {
152	                title: app.msg.music_on_hold  // Музыка на удержании
153	            }),
154	            Ext.create('app.module.Playlist_Grid', {
155	                title: app.msg.playlist  // Плейлист
156	            }) 
157	            ]
158	        }),
159	       
160	        Ext.create('app.Card_Panel', {
161	            title: app.msg.call_logs,  // История звонков
162	            items: [
163	            Ext.create('app.module.Active_calls_Grid', {
164	                title: app.msg.active_calls  // Активные звонки
165	            }),
166	            Ext.create('app.module.Call_logs_Grid', {
167	                title: app.msg.call_logs  // История звонков
168	            })
169	            ]
170	        }),
171	       
172	        Ext.create('app.Card_Panel', {
173	            title: app.msg.settings,  // Настройки
174	            items: [ 
175	            Ext.create('app.module.Users_Grid', {
176	                title: app.msg.users  // Управление доступом
177	            }), 
178	            Ext.create('app.module.Network_Settings_Panel', {
179	                title: app.msg.network_settings  // Сетевые настройки
180	            }), 
181	            Ext.create('app.module.Mail_Settings_Panel', {
182	                title: app.msg.mail_settings  // Почтовые уведомления
183	            }),
184	            /*
185	            Ext.create('app.module.Ntn_Settings_Grid',{
186	                title:app.msg.notification_settings
187	            }), 
188	            Ext.create('app.module.Update_Panel',{
189	                title:app.msg.update
190	            }),  */
191	
192	            {
193	                title    : app.msg.reboot_pbx,  // Перезагрузка АТС
194	               
195	                handler  : function() {
196	                    var fn = function(btn) {
197	                        if (btn == 'yes') {
198	                            var box = Ext.MessageBox.wait(app.msg.wait_reboot, app.msg.performing_actions);
199	                            // Пожалуйста, подождите пока происходит перезагрузка АТС
200	                            // Выполнение действий
201	                            app.request(
202	                            {
203	                                action : 'reboot'
204	                            },
205	                            function(result) {
206	                                if (!result.message)
207	                                    box.hide();
208	                            // console.log(result)
209	                            });
210	                        }
211	                    };
212	                    Ext.MessageBox.show({
213	                        title    : app.msg.performing_actions,  // Выполнение действий
214	                        msg      : app.msg.reboot_pbx_question,  // Выполнить перезагрузку АТС?
215	                        buttons  : Ext.MessageBox.YESNOCANCEL,
216	                        fn       : fn,
217	                        animEl   : 'mb4',
218	                        icon     : Ext.MessageBox.QUESTION
219	                    });
220	
221	                }
222	            },
223	           
224	            {
225	                title    : app.msg.update,   // Обновление
226	               
227	                handler  : function() {
228	                    var fn = function(btn) {
229	                        if (btn == 'yes') {
230	                            var box = Ext.MessageBox.wait(app.msg.wait_checkforupdates, app.msg.performing_actions);
231	                            // Пожалуйста, подождите пока происходит проверка обновлений АТС
232	                            // Выполнение действий
233	                            app.request(
234	                            {
235	                                action : 'checkforupdates'
236	                            },
237	                            function(result) {
238	                                if (!result.message)
239	                                    box.hide();
240	                                if (result.update_exists) {
241	                                    var fn_update = function(btn) {
242	                                        if (btn == 'yes') {
243	                                            var box = Ext.MessageBox.wait(app.msg.wait_update, app.msg.performing_actions);
244	                                            // Пожалуйста, подождите пока происходит установка обновлений
245	                                            // Выполнение действий
246	                                            var polling_time = 5000;
247	                                            // Ext.MessageBox.maxHeight = 400; 
248	                                            var intervalID = setInterval(function() {
249	                                                app.request(
250	                                                {
251	                                                    action : 'get_update_out'
252	                                                },
253	                                                function(result) {
254	                                                    if (result.data)
255	                                                        box.updateText(app.msg.wait_update+'<br>'+result.data);
256	                                                }
257	                                                );
258	
259	                                            }, polling_time);
260	                                            app.request(
261	                                            {
262	                                                action : 'install_update'
263	                                            },
264	                                            function(result){
265	                                                clearInterval(intervalID);
266	                                                if (!result.message)
267	                                                    box.hide();
268	                                            }
269	                                            );
270	                                        }
271	                                    };
272	                                    Ext.MessageBox.show({
273	                                        title    : app.msg.performing_actions,  // Выполнение действий
274	                                        msg      : app.msg.update_install +' '+result.update_exists+'?',  // Найдено обновление. Установить обновление
275	                                        buttons  : Ext.MessageBox.YESNOCANCEL,
276	                                        fn       : fn_update,
277	                                        animEl   : 'mb4',
278	                                        icon     : Ext.MessageBox.QUESTION
279	                                    });
280	                                   
281	                                }
282	                            });
283	                        }
284	                    };       
285	                    Ext.MessageBox.show({
286	                        title    : app.msg.performing_actions,  // Выполнение действий
287	                        msg      : app.msg.checkforupdates,  // Проверить на наличие обновлений?
288	                        buttons  : Ext.MessageBox.YESNOCANCEL,
289	                        fn       : fn,
290	                        animEl   : 'mb4',
291	                        icon     : Ext.MessageBox.QUESTION
292	                    });
293	
294	                }
295	            }
296	           
297	            ]
298	        })
299	       
300	        //{
301	        //    title: app.msg.attendant,layout: 'anchor',
302	        //    items: [{height:100,border: false,html:'test message'},Ext.create('app.module.Prompts_Grid'/*,{height:300})]
303	        //}
304	   
305	        ] 
306	    }],
307	   
308	    initComponent : function() {
309	        this.items[0].title =
310	        // '<div class="x-box-inner" style="padding-left: 20px; padding-right: 20px; height: 60px; background-color: #D5EAF3">'+
311	        '<div class="x-box-inner" style="padding-left: 20px; padding-right: 20px; height: 60px">'+
312	        '<img class="logo" src="js/app/images/logo_ts.png" height="60px" alt="TS" border="0" align="left">'+
313	        '<p align="right"><a href="#" onclick="app.logout(); return false">' + app.msg.logout + '</a></p>'+
314	        '<p align="right">' + app.msg.user + ': ' + this.user_name + '</p>'+
315	        // '<p align="right"><a target="_blank" href="http://ats.digt.local/bugtracker/">BUG TRACKER</a></p>'+
316	        '</div>';
317	 
318	        this.callParent(arguments);
319	       
320	        Ext.TaskManager.start({
321	            run : function() {
322	                Ext.StoreMgr.each(function(item, index, length) {
323	                    if (item.storeId == 'statistic') {
324	                        if (item.autorefresh) item.load(); 
325	                    // console.log(item.storeId + ":item.autorefresh-:" + item.autorefresh);
326	                    };
327	                    if (Ext.getCmp(item.storeId + '_grid'))
328	                        if (app.active_store == item.storeId && item.autorefresh && !this.dirtyMark) item.load();
329	                })
330	            },
331	            interval : app.refreshTime
332	        });
333	    }   
334	});