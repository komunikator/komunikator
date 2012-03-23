/*
 * SpanDSP - a series of DSP components for telephony
 *
 * private/t31.h - A T.31 compatible class 1 FAX modem interface.
 *
 * Written by Steve Underwood <steveu@coppice.org>
 *
 * Copyright (C) 2004 Steve Underwood
 *
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License version 2.1,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 * $Id: t31.h,v 1.1 2008/10/13 13:14:01 steveu Exp $
 */

#if !defined(_SPANDSP_PRIVATE_T31_H_)
#define _SPANDSP_PRIVATE_T31_H_

/*!
    Analogue FAX front end channel descriptor. This defines the state of a single working
    instance of an analogue line FAX front end.
*/
typedef struct
{
    fax_modems_state_t modems;

    /*! The transmit signal handler to be used when the current one has finished sending. */
    span_tx_handler_t *next_tx_handler;
    void *next_tx_user_data;

    /*! \brief No of data bits in current_byte. */
    int bit_no;
    /*! \brief The current data byte in progress. */
    int current_byte;

    /*! \brief Rx power meter, used to detect silence. */
    power_meter_t rx_power;
    /*! \brief Last sample, used for an elementary HPF for the power meter. */
    int16_t last_sample;
    /*! \brief The current silence threshold. */
    int32_t silence_threshold_power;

    /*! \brief Samples of silence heard */
    int silence_heard;
} t31_audio_front_end_state_t;

/*!
    Analogue FAX front end channel descriptor. This defines the state of a single working
    instance of an analogue line FAX front end.
*/
typedef struct
{
    t38_core_state_t t38;

    /*! \brief The number of octets to send in each image packet (non-ECM or ECM) at the current
               rate and the current specified packet interval. */
    int octets_per_data_packet;

    int timed_step;

    int ms_per_tx_chunk;
    int merge_tx_fields;

    /*! \brief TRUE is there has been some T.38 data missed */
    int missing_data;

    /*! \brief The number of times an indicator packet will be sent. Numbers greater than one
               will increase reliability for UDP transmission. Zero is valid, to suppress all
               indicator packets for TCP transmission. */
    int indicator_tx_count;

    /*! \brief The number of times a data packet which ends transmission will be sent. Numbers
               greater than one will increase reliability for UDP transmission. Zero is not valid. */
    int data_end_tx_count;

    /*! \brief The current T.38 data type being transmitted */
    int current_tx_data_type;
    /*! \brief The next queued tramsit indicator */
    int next_tx_indicator;

    int trailer_bytes;

    struct
    {
        uint8_t buf[T31_T38_MAX_HDLC_LEN];
        int len;
    } hdlc_rx;

    int current_rx_type;
    int current_tx_type;

    int32_t next_tx_samples;
    int32_t timeout_rx_samples;
    /*! \brief A "sample" count, used to time events */
    int32_t samples;
} t31_t38_front_end_state_t;

/*!
    T.31 descriptor. This defines the working state for a single instance of
    a T.31 FAX modem.
*/
struct t31_state_s
{
    at_state_t at_state;
    t31_modem_control_handler_t *modem_control_handler;
    void *modem_control_user_data;

    t31_audio_front_end_state_t audio;
    t31_t38_front_end_state_t t38_fe;
    /*! TRUE if working in T.38 mode. */
    int t38_mode;

    /*! HDLC buffer, for composing an HDLC frame from the computer to the channel. */
    struct
    {
        uint8_t buf[T31_MAX_HDLC_LEN];
        int len;
        int ptr;
        /*! \brief TRUE when the end of HDLC data from the computer has been detected. */
        int final;
    } hdlc_tx;
    /*! Buffer for data from the computer to the channel. */
    struct
    {
        uint8_t data[T31_TX_BUF_LEN];
        /*! \brief The number of bytes stored in transmit buffer. */
        int in_bytes;
        /*! \brief The number of bytes sent from the transmit buffer. */
        int out_bytes;
        /*! \brief TRUE if the flow of real data has started. */
        int data_started;
        /*! \brief TRUE if holding up further data into the buffer, for flow control. */
        int holding;
        /*! \brief TRUE when the end of non-ECM data from the computer has been detected. */
        int final;
    } tx;

    /*! TRUE if DLE prefix just used */
    int dled;

	/*! \brief Samples of silence awaited, as specified in a "wait for silence" command */
    int silence_awaited;

    /*! \brief The current bit rate for the FAX fast message transfer modem. */
    int bit_rate;
    /*! \brief TRUE if a valid HDLC frame has been received in the current reception period. */
    int rx_frame_received;

    /*! \brief Samples elapsed in the current call */
    int64_t call_samples;
    int64_t dte_data_timeout;

    /*! \brief The currently queued modem type. */
    int modem;
    /*! \brief TRUE when short training mode has been selected by the computer. */
    int short_train;
    queue_state_t *rx_queue;

    /*! \brief Error and flow logging control */
    logging_state_t logging;
};

#endif
/*- End of file ------------------------------------------------------------*/
