
/Эл╤;┼∙.╜U1zj╦ЬXжз└z9512a0ef062a                                            PЛ  ЄН  
   _uposition   _u_uposition                  RЛ  ёН     _ucolor	   _u_ucolor                 PЛ  ЄН     _ulocalCoord   _u_ulocalCoord                    RЛ  ЄН     _usk_RTAdjust   _u_usk_RTAdjust                                  [Л  ЄН      _uuCoordTransformMatrix_0_Stage0"   _u_uuCoordTransformMatrix_0_Stage0                                  RЛ  ёН     _uuleftBorderColor_Stage1_c0   _u_uuleftBorderColor_Stage1_c0                                  RЛ  ёН     _uurightBorderColor_Stage1_c0   _u_uurightBorderColor_Stage1_c0                                    ёН     _uubias_Stage1_c0_c0   _u_uubias_Stage1_c0_c0                                    ёН     _uuscale_Stage1_c0_c0   _u_uuscale_Stage1_c0_c0                                  RЛ  ёН     _uuscaleAndTranslate_Stage3   _u_uuscaleAndTranslate_Stage3                                  RЛ  ёН     _uuTexDom_Stage3   _u_uuTexDom_Stage3                                  QЛ  ёН     _uuDecalParams_Stage3   _u_uuDecalParams_Stage3                                  ^Л         _uuTextureSampler_0_Stage1   _u_uuTextureSampler_0_Stage1                                  ^Л         _uuTextureSampler_0_Stage3   _u_uuTextureSampler_0_Stage3                                                                                                                                             	           
                           ММ                        	                                                          ▐  ┴  bТКб    ░                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ,  struct VS_OUTPUT
{
    float4 dx_Position : SV_Position;
    float4 gl_Position : TEXCOORD2;
    float4 gl_FragCoord : TEXCOORD3;
    float4 v0 : TEXCOORD0;
    float2 v1 : TEXCOORD1;
};
float3 vec3_ctor(float2 x0, float x1)
{
    return float3(x0, x1);
}
float4 vec4_ctor(float x0, float x1, float x2, float x3)
{
    return float4(x0, x1, x2, x3);
}
float4 vec4_ctor(float2 x0, float x1, float x2)
{
    return float4(x0, x1, x2);
}
// Uniforms

uniform float4 __usk_RTAdjust : register(c1);
uniform float3x3 __uuCoordTransformMatrix_0_Stage0 : register(c2);
#ifdef ANGLE_ENABLE_LOOP_FLATTEN
#define LOOP [loop]
#define FLATTEN [flatten]
#else
#define LOOP
#define FLATTEN
#endif

#define ATOMIC_COUNTER_ARRAY_STRIDE 4

// Attributes
static float2 __uposition = {0, 0};
static float4 __ucolor = {0, 0, 0, 0};
static float2 __ulocalCoord = {0, 0};

static float4 gl_Position = float4(0, 0, 0, 0);

// Varyings
static  float2 __uvTransformedCoords_0_Stage0 = {0, 0};
static  float4 __uvcolor_Stage0 = {0, 0, 0, 0};

cbuffer DriverConstants : register(b1)
{
    float4 dx_ViewAdjust : packoffset(c1);
    float2 dx_ViewCoords : packoffset(c2);
    float2 dx_ViewScale  : packoffset(c3);
};

@@ VERTEX ATTRIBUTES @@

VS_OUTPUT generateOutput(VS_INPUT input)
{
    VS_OUTPUT output;
    output.gl_Position = gl_Position;
    output.dx_Position.x = gl_Position.x;
    output.dx_Position.y = - gl_Position.y;
    output.dx_Position.z = (gl_Position.z + gl_Position.w) * 0.5;
    output.dx_Position.w = gl_Position.w;
    output.gl_FragCoord = gl_Position;
    output.v0 = __uvcolor_Stage0;
    output.v1 = __uvTransformedCoords_0_Stage0;

    return output;
}

VS_OUTPUT main(VS_INPUT input){
    initAttributes(input);

(__uvTransformedCoords_0_Stage0 = mul(transpose(__uuCoordTransformMatrix_0_Stage0), vec3_ctor(__ulocalCoord, 1.0)).xy);
(__uvcolor_Stage0 = __ucolor);
(gl_Position = vec4_ctor(__uposition.x, __uposition.y, 0.0, 1.0));
(gl_Position = vec4_ctor(((gl_Position.xy * __usk_RTAdjust.xz) + (gl_Position.ww * __usk_RTAdjust.yw)), 0.0, gl_Position.w));
return generateOutput(input);
}
   f  struct PS_INPUT
{
    float4 dx_Position : SV_Position;
    float4 gl_Position : TEXCOORD2;
    float4 gl_FragCoord : TEXCOORD3;
    float4 v0 : TEXCOORD0;
    float2 v1 : TEXCOORD1;
};

float2 vec2_ctor(float x0, float x1)
{
    return float2(x0, x1);
}
float4 vec4_ctor(float x0, float x1, float x2, float x3)
{
    return float4(x0, x1, x2, x3);
}
float4 vec4_ctor(float3 x0, float x1)
{
    return float4(x0, x1);
}
// Uniforms

uniform float4 __uuleftBorderColor_Stage1_c0 : register(c0);
uniform float4 __uurightBorderColor_Stage1_c0 : register(c1);
uniform float __uubias_Stage1_c0_c0 : register(c2);
uniform float __uuscale_Stage1_c0_c0 : register(c3);
uniform float4 __uuscaleAndTranslate_Stage3 : register(c4);
uniform float4 __uuTexDom_Stage3 : register(c5);
uniform float3 __uuDecalParams_Stage3 : register(c6);
static const uint __uuTextureSampler_0_Stage1 = 0;
static const uint __uuTextureSampler_0_Stage3 = 1;
uniform Texture2D<float4> textures2D[2] : register(t0);
uniform SamplerState samplers2D[2] : register(s0);
#ifdef ANGLE_ENABLE_LOOP_FLATTEN
#define LOOP [loop]
#define FLATTEN [flatten]
#else
#define LOOP
#define FLATTEN
#endif

#define ATOMIC_COUNTER_ARRAY_STRIDE 4

// Varyings
static  float2 __uvTransformedCoords_0_Stage0 = {0, 0};
static  float4 __uvcolor_Stage0 = {0, 0, 0, 0};

static float4 gl_Color[1] =
{
    float4(0, 0, 0, 0)
};
static float4 gl_FragCoord = float4(0, 0, 0, 0);

cbuffer DriverConstants : register(b1)
{
    float4 dx_ViewCoords : packoffset(c1);
    float3 dx_DepthFront : packoffset(c2);
    float2 dx_ViewScale : packoffset(c3);
    struct SamplerMetadata
    {
        int baseLevel;
        int internalFormatBits;
        int wrapModes;
        int padding;
        int4 intBorderColor;
    };
    SamplerMetadata samplerMetadata[2] : packoffset(c4);
};

#define GL_USES_FRAG_COLOR
float4 gl_texture2D(uint samplerIndex, float2 t, float bias)
{
    return textures2D[samplerIndex].SampleBias(samplers2D[samplerIndex], float2(t.x, t.y), bias);
}

#define GL_USES_FRAG_COORD
float atan_emu(float y, float x)
{
    if(x == 0 && y == 0) x = 1;
    return atan2(y, x);
}


float4 mod_emu(float4 x, float4 y)
{
    return x - y * floor(x / y);
}


@@ PIXEL OUTPUT @@

PS_OUTPUT main(PS_INPUT input){
    float rhw = 1.0 / input.gl_FragCoord.w;
    gl_FragCoord.x = input.dx_Position.x;
    gl_FragCoord.y = input.dx_Position.y;
    gl_FragCoord.z = (input.gl_FragCoord.z * rhw) * dx_DepthFront.x + dx_DepthFront.y;
    gl_FragCoord.w = rhw;
    __uvcolor_Stage0 = input.v0;
    __uvTransformedCoords_0_Stage0 = input.v1.xy;

float4 __uoutputColor_Stage01039 = {0.0, 0.0, 0.0, 0.0};
{
(__uoutputColor_Stage01039 = __uvcolor_Stage0);
}
float4 __uoutput_Stage11040 = {0.0, 0.0, 0.0, 0.0};
{
float4 __uchild1041 = {0.0, 0.0, 0.0, 0.0};
{
float4 __u_child1_c01042 = {0.0, 0.0, 0.0, 0.0};
{
float __uangle1043 = {0.0};
{
(__uangle1043 = atan_emu((-__uvTransformedCoords_0_Stage0.y), (-__uvTransformedCoords_0_Stage0.x)));
}
float __ut1044 = ((((__uangle1043 * 0.15915494) + 0.5) + __uubias_Stage1_c0_c0) * __uuscale_Stage1_c0_c0);
(__u_child1_c01042 = vec4_ctor(__ut1044, 1.0, 0.0, 0.0));
}
float4 __ut1045 = __u_child1_c01042;
if ((__ut1045.x < 0.0))
{
(__uchild1041 = __uuleftBorderColor_Stage1_c0);
}
else
{
if ((__ut1045.x > 1.0))
{
(__uchild1041 = __uurightBorderColor_Stage1_c0);
}
else
{
float4 __u_child0_c01046 = {0.0, 0.0, 0.0, 0.0};
float4 __u_childInput_c0_c11047 = __ut1045;
{
float2 __ucoord1048 = vec2_ctor(__u_childInput_c0_c11047.x, 0.5);
(__u_child0_c01046 = gl_texture2D(__uuTextureSampler_0_Stage1, __ucoord1048, -0.5).xyzw);
}
(__uchild1041 = __u_child0_c01046);
}
}
}
(__uoutput_Stage11040 = (__uchild1041 * __uoutputColor_Stage01039.w));
}
float4 __uoutput_Stage21049 = {0.0, 0.0, 0.0, 0.0};
{
(__uoutput_Stage21049 = __uoutput_Stage11040);
float __uvalue1050 = {0.0};
{
float4 __umodValues1051 = mod_emu(vec4_ctor(gl_FragCoord.x, gl_FragCoord.y, gl_FragCoord.x, gl_FragCoord.y), float4(2.0, 2.0, 4.0, 4.0));
float4 __ustepValues1052 = step(__umodValues1051, float4(1.0, 1.0, 2.0, 2.0));
(__uvalue1050 = (dot(__ustepValues1052, float4(0.5, 0.25, 0.125, 0.0625)) - 0.46875));
}
(__uoutput_Stage21049 = vec4_ctor(clamp((__uoutput_Stage21049.xyz + (__uvalue1050 * 0.0039215689)), 0.0, __uoutput_Stage21049.w), __uoutput_Stage21049.w));
}
float4 __uoutput_Stage31053 = {0.0, 0.0, 0.0, 0.0};
{
float2 __ucoords1054 = ((gl_FragCoord.xy * __uuscaleAndTranslate_Stage3.xy) + __uuscaleAndTranslate_Stage3.zw);
{
float2 __uorigCoord1055 = __ucoords1054;
float2 __uclampedCoord1056 = clamp(__uorigCoord1055.xy, __uuTexDom_Stage3.xy, __uuTexDom_Stage3.zw);
float4 __uinside1057 = gl_texture2D(__uuTextureSampler_0_Stage3, __uclampedCoord1056, -0.5).xxxx;
float __uerr1058 = max((abs((__uclampedCoord1056.x - __uorigCoord1055.x)) * __uuDecalParams_Stage3.x), (abs((__uclampedCoord1056.y - __uorigCoord1055.y)) * __uuDecalParams_Stage3.y));
if ((__uerr1058 > __uuDecalParams_Stage3.z))
{
(__uerr1058 = 1.0);
}
else
{
if ((__uuDecalParams_Stage3.z < 1.0))
{
(__uerr1058 = 0.0);
}
}
(__uoutput_Stage31053 = lerp(__uinside1057, float4(0.0, 0.0, 0.0, 0.0), __uerr1058));
}
}
{
(gl_Color[0] = (__uoutput_Stage21049 * __uoutput_Stage31053));
}
return generateOutput();
}
                                        RЛ  	   gl_Color0   gl_Color[0]    ╩  struct GS_INPUT
{
    float4 dx_Position : SV_Position;
    float4 gl_Position : TEXCOORD2;
    float4 gl_FragCoord : TEXCOORD3;
    float4 v0 : TEXCOORD0;
    float2 v1 : TEXCOORD1;
};

struct GS_OUTPUT
{
    float4 dx_Position : SV_Position;
    float4 gl_Position : TEXCOORD2;
    float4 gl_FragCoord : TEXCOORD3;
    float4 v0 : TEXCOORD0;
    float2 v1 : TEXCOORD1;
};

void copyVertex(inout GS_OUTPUT output, GS_INPUT input, GS_INPUT flatinput)
{
    output.gl_Position = input.gl_Position;
    output.v0 = input.v0; 
    output.v1 = input.v1; 
    output.gl_FragCoord = input.gl_FragCoord;
#ifndef ANGLE_POINT_SPRITE_SHADER
    output.dx_Position = input.dx_Position;
#endif  // ANGLE_POINT_SPRITE_SHADER
}
      Б   q   Б   P  DXBC╦9О)ь╩■╟?|ьt>C,
   P     4   └  $  ─  ┤  RDEFД     h      <    ■  ┴  \  RD11<          (   $          \                              $Globals ллл\      А   P           ╨            ш                             ,      8                      __usk_RTAdjust float4 лл                            ▀   __uuCoordTransformMatrix_0_Stage0 float3x3 л                            .  Microsoft (R) HLSL Shader Compiler 10.1 ISGN\         P                    P                  P                  TEXCOORD лллOSGNШ         А                    М                   М                   М                    М                  SV_Position TEXCOORD лллSHEXш  P  z   j Y  FО         _  2     _  Є    _  2    g  Є         e  Є     e  Є     e  Є     e  2     h     6  ┬      @             ?  А?2  2      F     ЖА         ╓Е         6  "       АA       6        
      6  2     F      6  2     F      6  ┬     @                А?6  ┬     @                А?6  Є     F    6  2      F    6  B      @    А?       FВ         F       "     FВ         F     >  STATФ                                                                          	                                                                             рМ  °  DXBCr6┬TРЦ	!!шdy)~┤р   °     4   ш  И  ╝  \  RDEFм           <       ┴  Д  RD11<          (   $          ▄                            ъ                           °                                                                               samplers2D[0] samplers2D[1] textures2D[0] textures2D[1] $Globals ллл     8  p           P            x                      Ь           x                      ╗            ╪                      №  0         ╪                        @         x                      0  P         x                      B  `         `                      __uuleftBorderColor_Stage1_c0 float4 ллл                            n  __uurightBorderColor_Stage1_c0 __uubias_Stage1_c0_c0 float л                             ╤  __uuscale_Stage1_c0_c0 __uuscaleAndTranslate_Stage3 __uuTexDom_Stage3 __uuDecalParams_Stage3 float3                             Y  Microsoft (R) HLSL Shader Compiler 10.1 ISGNШ         А                   М                   М                   М                   М                  SV_Position TEXCOORD лллOSGN,                               SV_TARGET ллSHEXШ  P   &  j Y  FО         Z   `     Z   `    X  p     UU  X  p    UU  d  2        b В    b 2    e  Є      h       2      FАA      @                                
      7  
      
      @    А?
АA      3  	"      
 АБ       АБ      4  	B      
 АБ       АБ        
B      @    А?  А?  А?  А?*      8  "      *            8  B                  2  	В      *      @  _ок<@  6Zо╜2  	В      *      :      @  тv8>2  	В      *      :      @  й╛2  	B      *      :      @  8ў?8  В      *            1  	     
 АБ       АБ      2  	В      :      @     └@  █╔?  В      
     :      2  	"            *      :      1  B      
      
 АA         B      *      @  █I└   "      *            3  B      
      АA      4        
      АA      1  B      *      * АA               
      
 АA               
      *      7  
      
       АA             2  
      
      @  Г∙">
А                  
      @     ?8        
      
А         1  B      
      @       *      6  Є     FО            1  B      @    А?
       *      6  Є     FО           6  "      @     ?J  Н┬  АCU Є     F      F~      `     @     ┐    8  Є      F    Ў    8  
Є     F     @     ?   ?  А>  А>A  Є     F    2  Є     FАA      @     @   @  А@  А@F       
Є     @    А?  А?   @   @F      
Є     F    @    А?  А?  А?  А?  
     F    @     ?  А>   >  А=        
     @    Ё╛2  r          @  БАА;БАА;БАА;    F     4  
r     F    @                  3  r      Ў     F    2  2     F     FА         цК         4  ┬         Д         3  ┬     ж    жО         J  Н┬  АCU      ц
    F~     `    @     ┐   2     F АA      ц
    8  	2     F АБ      FА         4            
     1  "     *А         
     1  B     *А         @    А?7  	     *     @      
     7  	          @    А?
     2  
     
     
 АA      
     8  Є      F          >  STATФ   B             -                                                                                                                                                                                            "ч
╪а╙ДЇES^u╡╜fХ┌╕vX▌1
$╨ЦЄЫposition"
_uposition( 0B          +
╥ЦёЫcolor"_ucolor( 0B          5
(╨ЦЄЫ
localCoord"_ulocalCoord( 0B          ,
*╥ЦЄЫsk_RTAdjust"_usk_RTAdjust( 0B R
P█ЦЄЫuCoordTransformMatrix_0_Stage0" _uuCoordTransformMatrix_0_Stage0( 0B "P
J╨ЦЄЫvTransformedCoords_0_Stage0"_uvTransformedCoords_0_Stage0( 0B   "4
.╥ЦёЫvcolor_Stage0"_uvcolor_Stage0( 0B   ".
(╥ЦЄЫgl_Position"gl_Position( 0B   *у
RеB░?▌LесБt=╓№V╓ёк¤═J
H╥ЦёЫuleftBorderColor_Stage1_c0"_uuleftBorderColor_Stage1_c0( 0B L
J╥ЦёЫurightBorderColor_Stage1_c0"_uurightBorderColor_Stage1_c0( 0B <
:╤ЦёЫuDecalParams_Stage3"_uuDecalParams_Stage3( 0B H
F╥ЦёЫuscaleAndTranslate_Stage3"_uuscaleAndTranslate_Stage3( 0B 9
7Ж(ёЫubias_Stage1_c0_c0"_uubias_Stage1_c0_c0( 0B ;
9Ж(ёЫuscale_Stage1_c0_c0"_uuscale_Stage1_c0_c0( 0B 2
0╥ЦёЫuTexDom_Stage3"_uuTexDom_Stage3( 0